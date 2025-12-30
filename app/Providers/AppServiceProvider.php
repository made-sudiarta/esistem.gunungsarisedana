<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TrxSukarela;
use App\Observers\TrxSukarelaObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Carbon;


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
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        TrxSukarela::observe(TrxSukarelaObserver::class);

        // Date::setLocale('id');
        Carbon::setLocale('id_ID');        
    }
}
