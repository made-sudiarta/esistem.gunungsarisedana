<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TrxSukarela;
use App\Observers\TrxSukarelaObserver;

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
        TrxSukarela::observe(TrxSukarelaObserver::class);
        
    }
}
