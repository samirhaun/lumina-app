<?php

// app/Providers/ViewServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('frontend::*', function($view){
            $view->with('productTypes',
                DB::table('product_types')->orderBy('name')->get()
            );
        });
    }

    public function register(){}
}
