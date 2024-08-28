<?php

namespace App\Providers;

use App\Services\ExternalApi\NYTApiService;
use App\Services\Api\ArticleService;
use Illuminate\Support\Facades\Schema;
use App\Services\ExternalApi\NewsApiOrgService;
use Illuminate\Support\ServiceProvider;
use App\Services\ExternalApi\TheGuardianApiService;
use App\Services\Api\NewsPreferencesService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ArticleService::class, function () {
            return new ArticleService([
                new NewsApiOrgService(),
                new TheGuardianApiService(),
                new NYTApiService(),
            ]);
        });

        $this->app->singleton(NewsPreferencesService::class, function () {
            return new NewsPreferencesService([
                new NewsApiOrgService(),
                new TheGuardianApiService(),
                new NYTApiService(),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
