<?php
namespace App\Observers;

use App\Models\AssetCapitalization;
use App\Models\AssetCapitalizationDetail;

class CapitalizationObserver
{
    /**
     * Listen to the AssetCapitalization deleted event.
     *
     * @param  AssetCapitalization  $fixedAssetDepreciationMaster
     * @return void
     */
    public function deleted(AssetCapitalization $assetCapitalization)
    {
        $assetCapitalizationDetail = AssetCapitalizationDetail::OfCapitalization($assetCapitalization->capitalizationID)->delete();
    }
}