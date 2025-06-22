<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App; // 

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
        // ADICIONE ESTA LINHA PARA FORÇAR O IDIOMA EM TODA A APLICAÇÃO
        App::setLocale(config('app.locale'));
    }
}
