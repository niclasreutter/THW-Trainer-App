<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Policies\OrtsverbandLernpoolPolicy;
use App\Policies\OrtsverbandLernpoolQuestionPolicy;

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
