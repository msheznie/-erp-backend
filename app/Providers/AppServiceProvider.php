<?php

namespace App\Providers;

use App\Models\AssetCapitalization;
use App\Models\AssetCapitalizationDetail;
use App\Models\AssetDisposalMaster;
use App\Models\FixedAssetDepreciationMaster;
use App\Observers\CapitalizationDetailObserver;
use App\Observers\CapitalizationObserver;
use App\Observers\DepreciationObserver;
use App\Observers\DisposalObserver;
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
        AssetCapitalization::observe(CapitalizationObserver::class);
        AssetDisposalMaster::observe(DisposalObserver::class);
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
