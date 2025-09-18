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
use App\Models\JobErrorLog;
use App\Models\JvMaster;
use App\Models\MatchDocumentMaster;
use App\Models\MaterielRequest;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\TaxLedgerDetail;
use App\Models\VatReturnFillingMaster;
use Carbon\Carbon;
use App\Models\DeliveryOrder;

class ValidateDocumentAmend
{
    public static function validateFinancePeriod($documentAutoId,$documentSystemID, $matchingMasterID = null)
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

                                $message = trans('custom.financial_period_inactive', [
                                    'dateFrom' => $dateFrom,
                                    'dateTo'   => $dateTo,
                                ]);

                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 4: // Payment Voucher
                    if($matchingMasterID !== null){
                        $PaySupplierInvoiceMaster = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $PaySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($documentAutoId);
                    }
                    if($PaySupplierInvoiceMaster){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$PaySupplierInvoiceMaster->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = trans('custom.financial_period_inactive', [
                                    'dateFrom' => $dateFrom,
                                    'dateTo'   => $dateTo,
                                ]);
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
                    if($matchingMasterID !== null){
                        $receiptVoucherMaster = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $receiptVoucherMaster = CustomerReceivePayment::find($documentAutoId);
                    }
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
            case 15: // DN - Debit Note
                    if($matchingMasterID !== null){
                        $debitNoteMasterData = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $debitNoteMasterData = DebitNote::find($documentAutoId);
                    }
                    if($debitNoteMasterData){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$debitNoteMasterData->companyFinancePeriodID)->first();
                        if($financePeriod){
                            if($financePeriod->isActive == 0 || $financePeriod->isCurrent == 0){
                                $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                                $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                                $message = trans('custom.financial_period_inactive', [
                                    'dateFrom' => $dateFrom,
                                    'dateTo'   => $dateTo,
                                ]);
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 19: // CN - Credit Note
                    if($matchingMasterID !== null){
                        $creditNoteMasterData = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $creditNoteMasterData = CreditNote::find($documentAutoId);
                    }
                    if($creditNoteMasterData){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$creditNoteMasterData->companyFinancePeriodID)->first();
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
            case 71: // Delivery Order
                    $deliveryOrder = DeliveryOrder::find($documentAutoId);
                    if($deliveryOrder){
                        $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$deliveryOrder->companyFinancePeriodID)->where('departmentSystemID',11)->first();
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
        $message = trans('custom.cannot_amend_document');
        $isjobErrorLogExist = JobErrorLog::where('documentSystemCode',$documentAutoId)
                                            ->where('documentSystemID',$documentSystemID)                                               
                                            ->count();
        switch ($documentSystemID) {
            case 41: // FADS - Asset Disposal
                    $assetDisposalData = AssetDisposalMaster::find($documentAutoId);
                    if($assetDisposalData){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)                                                
                                                ->count();
                        if($glPost == 0){
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
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
                        if($isjobErrorLogExist > 0){
                            return ['status' => true];
                        } else {
                            return ['status' => false,'message'=>$message];
                        }
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
                        if($isjobErrorLogExist > 0){
                            return ['status' => true];
                        } else {
                            return ['status' => false,'message'=>$message];
                        }
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
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                    break;
            case 71: // Delivery Order
                    $deliveryOrder = DeliveryOrder::find($documentAutoId);
                    if($deliveryOrder){
                        $glPost = GeneralLedger::where('documentSystemCode',$documentAutoId)
                                                ->where('documentSystemID',$documentSystemID)
                                                ->count();
                        if($glPost == 0){
                            if($isjobErrorLogExist > 0){
                                return ['status' => true];
                            } else {
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

    public static function validateFinanceYear($documentAutoId,$documentSystemID, $matchingMasterID = null)
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
                    if($matchingMasterID !== null){
                        $receiptVoucherMaster = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $receiptVoucherMaster = CustomerReceivePayment::find($documentAutoId);
                    }
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
                    if($matchingMasterID !== null){
                        $PaySupplierInvoiceMaster = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $PaySupplierInvoiceMaster = PaySupplierInvoiceMaster::find($documentAutoId);
                    }
                    if($PaySupplierInvoiceMaster){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$PaySupplierInvoiceMaster->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                                $message = trans('custom.financial_year_inactive', [
                                    'dateFrom' => $dateFrom,
                                    'dateTo'   => $dateTo,
                                ]);
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

                                $message = trans('custom.financial_year_inactive', [
                                    'dateFrom' => $dateFrom,
                                    'dateTo'   => $dateTo,
                                ]);
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
            case 15: // DN - Debit Note
                    if($matchingMasterID !== null){
                        $debitNoteMasterData = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $debitNoteMasterData = DebitNote::find($documentAutoId);
                    }
                    if($debitNoteMasterData){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$debitNoteMasterData->companyFinanceYearID)->first();
                        if($financeYear){
                            if($financeYear->isActive == 0 || $financeYear->isCurrent == 0){
                                $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                                $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');
                                
                                $message = trans('custom.financial_year_inactive', [
                                    'dateFrom' => $dateFrom,
                                    'dateTo'   => $dateTo,
                                ]);
                                return ['status' => false,'message'=>$message];
                                
                                return ['status' => false,'message'=>$message];
                            }
                        }
                    }
                break;
            case 19: // CN - Credit Note
                    if($matchingMasterID !== null){
                        $creditNoteMasterData = MatchDocumentMaster::find($matchingMasterID);
                    } else {
                        $creditNoteMasterData = CreditNote::find($documentAutoId);
                    }
                    if($creditNoteMasterData){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$creditNoteMasterData->companyFinanceYearID)->first();
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
            case 71: // Delivery Order
                    $deliveryOrder = DeliveryOrder::find($documentAutoId);
                    if($deliveryOrder){
                        $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$deliveryOrder->companyFinanceYearID)->first();
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

    public static function validateVatReturnFilling($documentAutoID,$documentSystemID,$companySystemID) {
        $vatReturnFillingDetails = TaxLedgerDetail::with(['vat_return_filling_details'])
            ->where('documentMasterAutoID', $documentAutoID)
            ->where('companySystemID', $companySystemID)
            ->where('documentSystemID', $documentSystemID)
            ->whereNotNull('returnFilledDetailID')
            ->first();

        if($vatReturnFillingDetails) {
            $vatReturnFillingState = VatReturnFillingMaster::find($vatReturnFillingDetails->vat_return_filling_details->vatReturnFillingID);
            if(isset($vatReturnFillingState) && $vatReturnFillingState->confirmedYN == 1) {
                return [
                    'status' => false,
                    'message' => trans('custom.pulled_to_vat_return'). $vatReturnFillingState->returnFillingCode
                ];
            }
        }

        return ['status' => true];
    }

    public static function validateCLoseFinanceYear($documentSystemID, $matchingMasterID)
    {

        switch ($documentSystemID) {
            case 4: // Payment Voucher Matching
                $matchMaster = MatchDocumentMaster::find($matchingMasterID);
                if($matchMaster){
                    $financeYear = CompanyFinanceYear::where('companyFinanceYearID',$matchMaster->companyFinanceYearID)->first();
                    if($financeYear){

                        if($financeYear->isClosed == -1){
                            $dateFrom = (new Carbon($financeYear->bigginingDate))->format('d/m/Y');
                            $dateTo = (new Carbon($financeYear->endingDate))->format('d/m/Y');

                            $message = 'The Financial Year '.$dateFrom.' | '.$dateTo. ' on which this document was posted is closed, can’t refer back the matching';
                            return ['status' => false,'message'=>$message];
                        }
                    }
                }
                break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];
        }
    }

    public static function validateCLoseFinancePeriod($documentSystemID, $matchingMasterID)
    {
        
        switch ($documentSystemID) {
            case 4: // Payment Voucher Matching
                $matchMaster = MatchDocumentMaster::find($matchingMasterID);
                if($matchMaster){
                    $financePeriod = CompanyFinancePeriod::where('companyFinancePeriodID',$matchMaster->companyFinancePeriodID)->first();
                    if($financePeriod){
                        if($financePeriod->isClosed == -1){
                            $dateFrom = (new Carbon($financePeriod->dateFrom))->format('d/m/Y');
                            $dateTo = (new Carbon($financePeriod->dateTo))->format('d/m/Y');

                            $message = 'The Financial Period '.$dateFrom.' | '.$dateTo. ' on which this document was posted is closed, can’t refer back the matching';
                            return ['status' => false,'message'=>$message];
                        }
                    }
                }
                break;
            default:
                return ['status' => false,'message'=>'Document ID not found'];
        }
    }

}
