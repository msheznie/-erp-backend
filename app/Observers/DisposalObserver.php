<?php

namespace App\Observers;

use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\FixedAssetMaster;

class DisposalObserver
{
    /**
     * Listen to the AssetDisposalMaster deleted event.
     *
     * @param  AssetDisposalMaster $assetDisposalMaster
     * @return void
     */
    public function deleted(AssetDisposalMaster $assetDisposalMaster)
    {
        $assetCapitalizationDetail = AssetDisposalDetail::ofMaster($assetDisposalMaster->assetdisposalMasterAutoID)->get();
        if (count($assetCapitalizationDetail) > 0) {
            foreach ($assetCapitalizationDetail as $val) {
                $detail = AssetDisposalDetail::find($val->assetDisposalDetailAutoID);
                $detail->delete();

                $updateAsset = FixedAssetMaster::find($val->faID)
                    ->update(['DIPOSED' => 0, 'selectedForDisposal' => 0, 'disposedDate' => null, 'assetdisposalMasterAutoID' => null]);
            }
        }
    }

}