<?php
namespace App\Observers;

use App\Jobs\CreateDepreciation;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;

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
        // CreateDepreciation::dispatch($fixedAssetDepreciationMaster->depMasterAutoID);
    }

    /**
     * Listen to the FixedAssetDepreciationMaster deleted event.
     *
     * @param  FixedAssetDepreciationMaster  $fixedAssetDepreciationMaster
     * @return void
     */
    public function deleted(FixedAssetDepreciationMaster $fixedAssetDepreciationMaster)
    {
        $fixedAssetDepreciationPeriod = FixedAssetDepreciationPeriod::OfDepreciation($fixedAssetDepreciationMaster->depMasterAutoID)->delete();
    }
}