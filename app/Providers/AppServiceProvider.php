<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Mail\SendQueuedMailable;
use App\Helpers\SmtpRateLimit;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Policies\OrtsverbandLernpoolPolicy;
use App\Policies\OrtsverbandLernpoolQuestionPolicy;
use App\Models\LearningSession;
use App\Policies\LearningSessionPolicy;
use App\Helpers\DomainHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerBladeDirectives();
        $this->registerQueueLogging();
    }

    /**
     * Register custom Blade directives for domain URLs.
     */
    protected function registerBladeDirectives(): void
    {
        // @appUrl('/path') - Generiert URL für app.thw-trainer.de
        Blade::directive('appUrl', function ($expression) {
            return "<?php echo \App\Helpers\DomainHelper::appUrl($expression); ?>";
        });

        // @landingUrl('/path') - Generiert URL für thw-trainer.de
        Blade::directive('landingUrl', function ($expression) {
            return "<?php echo \App\Helpers\DomainHelper::landingUrl($expression); ?>";
        });

        // @isLandingDomain - Prüft ob auf Landing-Domain
        Blade::if('landingDomain', function () {
            return DomainHelper::isLandingDomain();
        });

        // @isAppDomain - Prüft ob auf App-Domain
        Blade::if('appDomain', function () {
            return DomainHelper::isAppDomain();
        });
    }

    /**
     * Log queue job details to worker stdout (supervisor worker.log).
     */
    protected function registerQueueLogging(): void
    {
        $this->app['events']->listen(JobProcessed::class, function (JobProcessed $event) {
            $this->logQueuedMail($event->job, 'DONE');
        });

        // SMTP-Hourly-Limit (554): Innerhalb der Retry-Schleife (vor maxTries-Erschöpfung)
        // den Job mit Delay bis zur nächsten vollen Stunde + 10 Minuten neu releasen.
        $this->app['events']->listen(JobExceptionOccurred::class, function (JobExceptionOccurred $event) {
            $this->maybeRescheduleForSmtpRateLimit($event);
        });

        $this->app['events']->listen(JobFailed::class, function (JobFailed $event) {
            // SMTP-Hourly-Limit (554): Statt in failed_jobs liegen lassen → neu queuen.
            if ($this->tryRequeueForSmtpRateLimit($event)) {
                return;
            }

            $this->logQueuedMail($event->job, 'FAILED', $event->exception?->getMessage());
        });
    }

    /**
     * Fängt 554-Rate-Limit-Fehler während laufender Retries (bevor maxTries erreicht ist)
     * ab und releast den Job mit Verzögerung bis zur nächsten vollen Stunde + 10 Minuten.
     */
    protected function maybeRescheduleForSmtpRateLimit(JobExceptionOccurred $event): void
    {
        if (! SmtpRateLimit::isRateLimitError($event->exception)) {
            return;
        }

        // Job ist bereits final fehlgeschlagen oder anders behandelt → JobFailed übernimmt.
        if ($event->job->hasFailed() || $event->job->isReleased() || $event->job->isDeleted()) {
            return;
        }

        $delaySeconds = SmtpRateLimit::calculateRetryDelay();
        $event->job->release($delaySeconds);

        $this->logSmtpRateLimitRetry($event->job, $delaySeconds, 'released');
    }

    /**
     * Final fehlgeschlagene SendQueuedMailable-Jobs mit 554-Rate-Limit
     * werden neu in die Queue gelegt und aus failed_jobs entfernt.
     */
    protected function tryRequeueForSmtpRateLimit(JobFailed $event): bool
    {
        if (! SmtpRateLimit::isRateLimitError($event->exception)) {
            return false;
        }

        try {
            $payload = json_decode($event->job->getRawBody(), true);
            $command = @unserialize($payload['data']['command'] ?? '');

            if (! ($command instanceof SendQueuedMailable)) {
                return false;
            }

            $delaySeconds = SmtpRateLimit::calculateRetryDelay();

            Queue::connection($event->connectionName)->later(
                $delaySeconds,
                $command,
                '',
                $event->job->getQueue()
            );

            DB::table('failed_jobs')->where('uuid', $event->job->uuid())->delete();

            $this->logSmtpRateLimitRetry($event->job, $delaySeconds, 'requeued');

            return true;
        } catch (\Throwable $e) {
            Log::warning('SMTP rate limit retry failed: ' . $e->getMessage());
            return false;
        }
    }

    protected function logSmtpRateLimitRetry($job, int $delaySeconds, string $action): void
    {
        try {
            $payload = $job->payload();
            $command = unserialize($payload['data']['command'] ?? '');

            if ($command instanceof SendQueuedMailable) {
                $mailable = $command->mailable;
                $recipients = collect($mailable->to)->pluck('address')->implode(', ');
                $class = class_basename($mailable);
                $time = now()->format('H:i:s');
                $retryAt = now()->addSeconds($delaySeconds)->format('H:i');

                fwrite(
                    STDOUT,
                    "  [{$time}] {$class} → {$recipients} [SMTP-LIMIT] {$action}, Retry um {$retryAt} (+{$delaySeconds}s)\n"
                );
            }
        } catch (\Throwable) {
            // Logging darf nie den Job crashen
        }
    }

    protected function logQueuedMail($job, string $status, ?string $error = null): void
    {
        try {
            $payload = $job->payload();
            $command = unserialize($payload['data']['command'] ?? '');

            if ($command instanceof SendQueuedMailable) {
                $mailable = $command->mailable;
                $recipients = collect($mailable->to)->pluck('address')->implode(', ');
                $class = class_basename($mailable);
                $time = now()->format('H:i:s');

                $message = "  [{$time}] {$class} → {$recipients} [{$status}]";
                if ($error) {
                    $message .= " Fehler: {$error}";
                }

                fwrite(STDOUT, $message . "\n");
            }
        } catch (\Throwable) {
            // Logging darf nie den Job crashen
        }
    }

    /**
     * Register authorization policies.
     */
    protected function registerPolicies(): void
    {
        \Gate::policy(OrtsverbandLernpool::class, OrtsverbandLernpoolPolicy::class);
        \Gate::policy(OrtsverbandLernpoolQuestion::class, OrtsverbandLernpoolQuestionPolicy::class);
        \Gate::policy(LearningSession::class, LearningSessionPolicy::class);
    }
}
