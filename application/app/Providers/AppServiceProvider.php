<?php

namespace App\Providers;

use App\Services\CSVImportService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCSVImportService();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function registerCSVImportService()
    {
        $this->app->singleton(CSVImportService::class, function() {
            return new CSVImportService();
        });
    }
}
