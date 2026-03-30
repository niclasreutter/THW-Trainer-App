<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Mail\SendQueuedMailable;
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

        \App\Models\Notification::observe(\App\Observers\NotificationObserver::class);
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

        $this->app['events']->listen(JobFailed::class, function (JobFailed $event) {
            $this->logQueuedMail($event->job, 'FAILED', $event->exception?->getMessage());
        });
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
