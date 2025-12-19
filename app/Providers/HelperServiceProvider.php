<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        require_once app_path('helper/general_helper.php');
        require_once app_path('helper/Formula.php');
        require_once app_path('helper/email.php');
        require_once app_path('helper/inventory.php');
    }
}
