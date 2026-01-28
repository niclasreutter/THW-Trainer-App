<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Policies\OrtsverbandLernpoolPolicy;
use App\Policies\OrtsverbandLernpoolQuestionPolicy;
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
     * Register authorization policies.
     */
    protected function registerPolicies(): void
    {
        \Gate::policy(OrtsverbandLernpool::class, OrtsverbandLernpoolPolicy::class);
        \Gate::policy(OrtsverbandLernpoolQuestion::class, OrtsverbandLernpoolQuestionPolicy::class);
    }
}
