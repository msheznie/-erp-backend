<?php
namespace App\Observers;

use App\Models\AssetCapitalization;
use App\Models\AssetCapitalizationDetail;

class CapitalizationDetailObserver
{
    /**
     * Listen to the AssetCapitalizationDetail created event.
     *
     * @param  AssetCapitalizationDetail  $assetCapitalizationDetail
     * @return void
     */
    public function created(AssetCapitalizationDetail $assetCapitalizationDetail)
    {
        $master = AssetCapitalization::find($assetCapitalizationDetail->capitalizationID);
        $detailSUM = AssetCapitalizationDetail::selectRAW('SUM(assetNBVLocal) as assetNBVLocal, SUM(assetNBVRpt) as assetNBVRpt')->where('capitalizationID', $assetCapitalizationDetail->capitalizationID)->first();
        $detail = AssetCapitalizationDetail::where(['capitalizationID' => $assetCapitalizationDetail->capitalizationID])->get();
        if ($detail) {
            foreach ($detail as $val) {
                $allocatedAmountLocal = 0;
                $allocatedAmountRpt = 0;
                if($detailSUM->assetNBVLocal){
                    $allocatedAmountLocal = ($val->assetNBVLocal / $detailSUM->assetNBVLocal) * $master->assetNBVLocal;
                }

                if($detailSUM->assetNBVRpt){
                    $allocatedAmountRpt = ($val->assetNBVRpt / $detailSUM->assetNBVRpt) * $master->assetNBVRpt;
                }

                $detailArr["allocatedAmountLocal"] = $allocatedAmountLocal;
                $detailArr["allocatedAmountRpt"] = $allocatedAmountRpt;
                $assetCapitalizationDetail = AssetCapitalizationDetail::where('capitalizationDetailID',$val->capitalizationDetailID)->update($detailArr);

            }
        }
    }

    /**
     * Listen to the AssetCapitalizationDetail deleted event.
     *
     * @param  AssetCapitalizationDetail  $assetCapitalizationDetail
     * @return void
     */
    public function deleted(AssetCapitalizationDetail $assetCapitalizationDetail)
    {
        $master = AssetCapitalization::find($assetCapitalizationDetail->capitalizationID);
        $detailSUM = AssetCapitalizationDetail::selectRAW('SUM(assetNBVLocal) as assetNBVLocal, SUM(assetNBVRpt) as assetNBVRpt')->where('capitalizationID', $assetCapitalizationDetail->capitalizationID)->first();
        $detail = AssetCapitalizationDetail::where(['capitalizationID' => $assetCapitalizationDetail->capitalizationID])->get();
        if ($detail) {
            foreach ($detail as $val) {
                $allocatedAmountLocal = ($val->assetNBVLocal / $detailSUM->assetNBVLocal) * $master->assetNBVLocal;
                $allocatedAmountRpt = ($val->assetNBVRpt / $detailSUM->assetNBVRpt) * $master->assetNBVRpt;

                $detailArr["allocatedAmountLocal"] = $allocatedAmountLocal;
                $detailArr["allocatedAmountRpt"] = $allocatedAmountRpt;
                $assetCapitalizationDetail = AssetCapitalizationDetail::where('capitalizationDetailID',$val->capitalizationDetailID)->update($detailArr);

            }
        }
    }
}