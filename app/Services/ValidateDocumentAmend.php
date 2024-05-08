<?php

namespace App\Services;

use App\Models\AssetDisposalMaster;
use App\Models\BookInvSuppMaster;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CreditNote;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerReceivePayment;
use App\Models\DebitNote;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetMaster;
use App\Models\GeneralLedger;
use App\Models\GRVMaster;
use App\Models\JvMaster;
use App\Models\MaterielRequest;
use App\Models\PaySupplierInvoiceMaster;
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
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 4: // Payment Voucher
                    $PaySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($documentAutoId);
                    if($PaySupplierInvoiceMaster){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$PaySupplierInvoiceMaster->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 20: // Customer Invoice
                    $customerInvoiceMaster = CustomerInvoiceDirect::find($documentAutoId);
                    if($customerInvoiceMaster){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$customerInvoiceMaster->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 21: // Receipt Voucher
                    $receiptVoucherMaster = CustomerReceivePayment::find($documentAutoId);
                    if($receiptVoucherMaster){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$receiptVoucherMaster->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 17: // JV - Journal Voucher
                    $jvMaster = JvMaster::find($documentAutoId);
                    if($jvMaster){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$jvMaster->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];

        }

        return ['status' => true];
	}

    public static function validatePendingGlPost($documentAutoId, $documentSystemID, $matchingMasterID = null)
	{
        $message = 'You cannot amend this document now. The General Ledger posting is In-Progress';
        switch ($documentSystemID) {
            case 41: // FADS - Asset Disposal
                    $assetDisposalData = AssetDisposalMaster::find($documentAutoId);
                    if($assetDisposalData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)                                                
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 23: // FAD - Asset Depreciation
                    $assetDepreciatioData = FixedAssetDepreciationMaster::find($documentAutoId);
                    if($assetDepreciatioData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)                                                
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 22: // FA - Asset Costing
                    $assetCostingData = FixedAssetMaster::find($documentAutoId);
                    if($assetCostingData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)                                               
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 19: // CN - Credit Note
                    $creditNoteMasterData = CreditNote::find($documentAutoId);
                    if($creditNoteMasterData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->when($matchingMasterID !== null, function ($query) use ($matchingMasterID) {
                                                    return $query->where('matchDocumentMasterAutoID', $matchingMasterID);
                                                })                                                
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 15: // DN - Debit Note
                    $debitNoteMasterData = DebitNote::find($documentAutoId);
                    if($debitNoteMasterData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->when($matchingMasterID !== null, function ($query) use ($matchingMasterID) {
                                                    return $query->where('matchDocumentMasterAutoID', $matchingMasterID);
                                                })                                                
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 9: // MR - Material Request
                    $materialRequest = MaterielRequest::find($documentAutoId);
                    if($materialRequest){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 3: // GRV - Good Reciept Voucher
                    $grvMasterData = GRVMaster::find($documentAutoId);
                    if($grvMasterData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 11: // SI - Supplier Invoice
                    $bookInvSuppMasterData = BookInvSuppMaster::find($documentAutoId);
                    if($bookInvSuppMasterData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 4: // Payment Voucher
                    $PaySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($documentAutoId);
                    if($PaySupplierInvoiceMaster){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->when($matchingMasterID !== null, function ($query) use ($matchingMasterID) {
                                                    return $query->where('matchDocumentMasterAutoID', $matchingMasterID);
                                                })                                                
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                break;
            case 20: // Customer Invoice
                $customerInvoiceMaster = CustomerInvoiceDirect::find($documentAutoId);
                if($customerInvoiceMaster){
                    $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                            ->where('documentSystemID',$documentSystemID)
                                            ->count();
                    if($glPost == 0){
                        return ['status' => false,'message'=>$message];
                    }
                }
                break;
            case 21: // Receipt Voucher
                $receiptVoucherMaster = CustomerReceivePayment::find($documentAutoId);
                if($receiptVoucherMaster){
                    $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                            ->where('documentSystemID',$documentSystemID)
                                            ->when($matchingMasterID !== null, function ($query) use ($matchingMasterID) {
                                                    return $query->where('matchDocumentMasterAutoID', $matchingMasterID);
                                                })
                                            ->count();
                    if($glPost == 0){
                        return ['status' => false,'message'=>$message];
                    }
                }
                break;
            case 17: // JV - Journal Voucher
                    $jvMaster = JvMaster::find($documentAutoId);
                    if($jvMaster){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->count();
                        if($glPost == 0){
                            return ['status' => false,'message'=>$message];
                        }
                    }
                    break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];

        }
        return ['status' => true];
	}

    public static function validateFinanceYear($documentAutoId,$documentSystemID)
	{
        switch ($documentSystemID) {
            case 20: // Customer Invoice
                    $customerInvoiceMaster = CustomerInvoiceDirect::find($documentAutoId);
                    if($customerInvoiceMaster){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$customerInvoiceMaster->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                                $message = 'The Financial Year '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 21: // Receipt Voucher
                    $receiptVoucherMaster = CustomerReceivePayment::find($documentAutoId);
                    if($receiptVoucherMaster){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$receiptVoucherMaster->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                                $message = 'The Financial Year '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 4: // Payment Voucher
                    $PaySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($documentAutoId);
                    if($PaySupplierInvoiceMaster){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$PaySupplierInvoiceMaster->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                                $message = 'The Financial Year '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 11: // SI - Supplier Invoice
                    $bookInvSuppMasterData = BookInvSuppMaster::find($documentAutoId);

                    if($bookInvSuppMasterData){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$bookInvSuppMasterData->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                                $message = 'The Financial Year '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 17: // JV - Journal Voucher
                    $jvMaster = JvMaster::find($documentAutoId);
                    if($jvMaster){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$jvMaster->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                                $message = 'The Financial Year '.$dateFrom.' | '.$dateTo. ' on which this document was posted, needs to be active & current for this document to be reversed';
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];

        }

        return ['status' => true];
	}
}