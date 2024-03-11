<?php

namespace App\Services;

use App\Models\BookInvSuppMaster;
use App\Models\CompanyFinancePeriod;
use App\Models\GeneralLedger;
use Carbon\Carbon;

class ValidateDocumentAmend
{
    public static function validateFinancePeriod($documentAutoId,$documentSystemID)
	{
        switch ($documentSystemID) {
            case 11: // SI - Supplier Invoice
                    $bookInvSuppMasterData = BookInvSuppMaster::find($documentAutoId);

                    if($bookInvSuppMasterData){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$bookInvSuppMasterData->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 && $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 4: // Payment Voucher
                break;
            case 24: // Purchase return
                break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];

        }

        return ['status' => true];
	}

    public static function validatePendingGlPost($documentAutoId,$documentSystemID)
	{
        switch ($documentSystemID) {
            case 11: // SI - Supplier Invoice
                    $bookInvSuppMasterData = BookInvSuppMaster::find($documentAutoId);
                    if($bookInvSuppMasterData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentCode',$bookInvSuppMasterData->bookingInvCode)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->first();
                        if(!isset($glPost)){
                            $message = 'GL posting in progress. Cannot return back to amend now';
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 4: // Payment Voucher
                break;
            case 24: // Purchase return
                break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];

        }
        return ['status' => true];
	}
}