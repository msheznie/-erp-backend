<?php
namespace App\Observers;

use App\Jobs\CreateDepreciation;
use App\Models\FixedAssetDepreciationMaster;

class DepreciationObserver
{
    /**
     * Listen to the FixedAssetDepreciationMaster created event.
     *
     * @param  FixedAssetDepreciationMaster  $fixedAssetDepreciationMaster
     * @return void
     */
    public function created(FixedAssetDepreciationMaster $fixedAssetDepreciationMaster)
    {
        CreateDepreciation::dispatch($fixedAssetDepreciationMaster->depMasterAutoID);
    }
}