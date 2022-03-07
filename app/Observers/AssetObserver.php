<?php
namespace App\Observers;

use App\Jobs\CreateAccumulatedDepreciation;
use App\Models\FixedAssetMaster;
use Illuminate\Support\Facades\Log;
class AssetObserver
{
    /**
     * Listen to the FixedAssetMaster created event.
     *
     * @param  FixedAssetMaster  $fixedAssetMaster
     * @return void
     */
    public function created(FixedAssetMaster $fixedAssetMaster)
    {
       
        //CreateAccumulatedDepreciation::dispatch($fixedAssetMaster->faID);
    }


}