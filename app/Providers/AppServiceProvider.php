<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\KeywordExtractionService;
use App\Services\ContentBasedFilteringService;
use App\Services\YouTubeService;
use App\Services\CBFEvaluationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         // Register KeywordExtractionService
        $this->app->singleton(KeywordExtractionService::class, function ($app) {
            return new KeywordExtractionService();
        });

          $this->app->singleton(CBFEvaluationService::class, function ($app) {
            return new CBFEvaluationService();
        });

        // Register ContentBasedFilteringService
        $this->app->singleton(ContentBasedFilteringService::class, function ($app) {
            return new ContentBasedFilteringService(
                $app->make(KeywordExtractionService::class)
            );
        });

        // Register YouTubeService
        $this->app->singleton(YouTubeService::class, function ($app) {
            return new YouTubeService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
