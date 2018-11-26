<?php

namespace App\Providers;

use App\Models\AssetCapitalizationDetail;
use App\Models\FixedAssetDepreciationMaster;
use App\Observers\CapitalizationDetailObserver;
use App\Observers\DepreciationObserver;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing(function (JobFailed $event) {
            // $event->connectionName
            // $event->job
            // $event->exception
        });
        AssetCapitalizationDetail::observe(CapitalizationDetailObserver::class);
        FixedAssetDepreciationMaster::observe(DepreciationObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
