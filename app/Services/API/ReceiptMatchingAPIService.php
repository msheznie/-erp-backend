<?php

namespace App\Services\API;

use App\helper\CurrencyValidation;
use App\helper\CustomValidation;
use App\helper\Helper;
use App\helper\TaxService;
use App\Http\Controllers\AppBaseController;
use App\Models\AdvanceReceiptDetails;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\DirectReceiptDetail;
use App\Models\GeneralLedger;
use App\Models\MatchDocumentMaster;
use App\Models\SegmentMaster;
use App\Models\TaxVatCategories;
use App\Services\UserTypeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\AccountsReceivableLedger;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\CustomerInvoice;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\Taxdetail;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Services\GeneralLedger\GlPostedDateService;
use App\Services\TaxLedger\RecieptVoucherTaxLedgerService;
use Exception;

class ReceiptMatchingAPIService extends AppBaseController
{ 
    public static function createReceiptMatching($input)
    {
        $company = Company::find($input['companySystemID']);
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        if ($input['matchType'] == 1 || $input['matchType'] == 3) { // 1 - unallocation; 3 - advance receipt

           if($input['tableType'] == 1) {
            
               $directReceiptDetails = DirectReceiptDetail::where('directReceiptAutoID', $input['custReceivePaymentAutoID'])->first();
           }
            if($input['tableType'] == 2) {

                $directReceiptDetails = AdvanceReceiptDetails::where('custReceivePaymentAutoID', $input['custReceivePaymentAutoID'])->first();
            }

            if (empty($directReceiptDetails)) {
                return [
                    'status' => false,
                    'message' => "Details not found",
                    'type' => []
                ];
            }
            if($input['tableType'] == 1) {
                $customerReceivePaymentMaster = CustomerReceivePayment::find($directReceiptDetails->directReceiptAutoID);
            }
            if($input['tableType'] == 2) {
                $customerReceivePaymentMaster = CustomerReceivePayment::find($directReceiptDetails->custReceivePaymentAutoID);
            }

            if (empty($customerReceivePaymentMaster)) {
                return [
                    'status' => false,
                    'message' => 'Customer Receive Payment not found'
                ];
            }

            $existCheck = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                ->where('PayMasterAutoId', $customerReceivePaymentMaster->custReceivePaymentAutoID)
                ->where('matchingConfirmedYN', 0)
                ->where('documentSystemID', $customerReceivePaymentMaster->documentSystemID)
                ->where('serviceLineSystemID', $directReceiptDetails->serviceLineSystemID)
                ->first();

            if($existCheck){
                return [
                    'status' => false,
                    'message' => "A matching document for the selected receipt voucher is created and not confirmed. Please confirm the previously created document and try again.",
                    'type' => []
                ];
            }
            if($input['tableType'] == 1) {

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')
                    ->where('documentSystemID', $customerReceivePaymentMaster->documentSystemID)
                    ->where('companySystemID', $customerReceivePaymentMaster->companySystemID)
                    ->where('documentSystemCode', $directReceiptDetails->directReceiptAutoID)
                    ->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')
                    ->first();
            }

            if($input['tableType'] == 2) {

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')
                    ->where('documentSystemID', $customerReceivePaymentMaster->documentSystemID)
                    ->where('companySystemID', $customerReceivePaymentMaster->companySystemID)
                    ->where('documentSystemCode', $directReceiptDetails->custReceivePaymentAutoID)
                    ->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')
                    ->first();
            }

            if ($glCheck) {
                if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                    return [
                        'status' => false,
                        'message' => "Selected customer receive payment is not updated in general ledger. Please check again",
                        'type' => []
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'message' => "Selected customer receive payment is not updated in general ledger. Please check again",
                    'type' => []
                ];
            }

            $receiveAmountTotTrans = 0;
            $receiveAmountTotLocal = 0;
            $receiveAmountTotRpt = 0;
            $masterID = 0;

            if($input['matchType'] == 1){
                //get unallocation sum amount
                $unAllocationAmountSum = CustomerReceivePaymentDetail::selectRaw('erp_custreceivepaymentdet.bookingAmountTrans, 
                                                        addedDocumentSystemID, bookingInvCodeSystem, 
                                                        Sum(erp_custreceivepaymentdet.receiveAmountTrans) AS SumDetailAmountTrans, 
                                                        Sum(erp_custreceivepaymentdet.receiveAmountLocal) AS SumDetailAmountLocal,
                                                        Sum(erp_custreceivepaymentdet.receiveAmountRpt) AS SumDetailAmountRpt')
                    ->where('custReceivePaymentAutoID', $input['custReceivePaymentAutoID'])
                    ->where('bookingInvCode', '0')
                    ->groupBy('custReceivePaymentAutoID')
                    ->first();

                if ($unAllocationAmountSum) {
                    $receiveAmountTotTrans = $unAllocationAmountSum["SumDetailAmountTrans"];
                    $receiveAmountTotLocal = $unAllocationAmountSum["SumDetailAmountLocal"];
                    $receiveAmountTotRpt = $unAllocationAmountSum["SumDetailAmountRpt"];
                }
            }else if($input['matchType'] == 3){


                $directDetails = DirectReceiptDetail::selectRaw("SUM(localAmount) as SumDetailAmountLocal, 
                                                                 SUM(comRptAmount) as SumDetailAmountRpt,
                                                                 SUM(DRAmount) as SumDetailAmountTrans")
                                                                ->where('directReceiptAutoID', $directReceiptDetails->directReceiptAutoID)
                                                                ->where('serviceLineSystemID', $directReceiptDetails->serviceLineSystemID)
                                                                ->groupBy('serviceLineSystemID')
                                                                ->first();

                // advance receipt details
                $advReceiptDetails = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as SumDetailAmountLocal, 
                                                                    SUM(comRptAmount) as SumDetailAmountRpt,
                                                                    SUM(paymentAmount) as SumDetailAmountTrans")
                                                              ->where('custReceivePaymentAutoID', $directReceiptDetails->custReceivePaymentAutoID)->where('serviceLineSystemID', $directReceiptDetails->serviceLineSystemID)->groupBy('serviceLineSystemID')
                                                               ->first();



                if(isset($directDetails) && isset($directDetails['SumDetailAmountTrans']) && $directDetails['SumDetailAmountTrans'] == 0 && $advReceiptDetails){
                    $receiveAmountTotTrans = $advReceiptDetails["SumDetailAmountTrans"];
                    $receiveAmountTotLocal = $advReceiptDetails["SumDetailAmountLocal"];
                    $receiveAmountTotRpt = $advReceiptDetails["SumDetailAmountRpt"];
                    $masterID = $directReceiptDetails->custReceivePaymentAutoID;
                }else{
                    $receiveAmountTotTrans = $directDetails["SumDetailAmountTrans"];
                    $receiveAmountTotLocal = $directDetails["SumDetailAmountLocal"];
                    $receiveAmountTotRpt   = $directDetails["SumDetailAmountRpt"];
                    $masterID = $directReceiptDetails->directReceiptAutoID;

                }
            }

            $customerDetail = CustomerMaster::find($input['customerID']);
      
            $input['matchingType'] = 'AR';
            if($input['isAutoCreateDocument']){
                $input['PayMasterAutoId'] = $input['custReceivePaymentAutoID'];
            } else {
                $input['PayMasterAutoId'] = $masterID;
            }
            
            $input['serviceLineSystemID'] = $directReceiptDetails->serviceLineSystemID;
            $input['documentSystemID'] = $customerReceivePaymentMaster->documentSystemID;
            $input['documentID'] = $customerReceivePaymentMaster->documentID;
            $input['BPVcode'] = $customerReceivePaymentMaster->custPaymentReceiveCode;
            $input['BPVdate'] = $customerReceivePaymentMaster->custPaymentReceiveDate;
            if($input['isAutoCreateDocument']){
                $input['BPVNarration'] = $input['narration'];
            } else {
                $input['BPVNarration'] = $customerReceivePaymentMaster->narration;
            }
            //$input['directPaymentPayeeSelectEmp'] = $customerReceivePaymentMaster->PayeeSelectEmp;
            $input['directPaymentPayee'] = $customerDetail->CustomerName;
            $input['directPayeeCurrency'] = $customerReceivePaymentMaster->custTransactionCurrencyID;
            $input['BPVsupplierID'] = $customerReceivePaymentMaster->customerID;
            $input['supplierGLCodeSystemID'] = $customerReceivePaymentMaster->customerGLCodeSystemID;
            $input['supplierGLCode'] = $customerReceivePaymentMaster->customerGLCode;
            $input['supplierTransCurrencyID'] = $customerReceivePaymentMaster->custTransactionCurrencyID;
            $input['supplierTransCurrencyER'] = $customerReceivePaymentMaster->custTransactionCurrencyER;
            $input['supplierDefCurrencyID'] = $customerReceivePaymentMaster->custTransactionCurrencyID;
            $input['supplierDefCurrencyER'] = $customerReceivePaymentMaster->custTransactionCurrencyER;
            $input['localCurrencyID'] = $customerReceivePaymentMaster->localCurrencyID;
            $input['localCurrencyER'] = $customerReceivePaymentMaster->localCurrencyER;
            $input['companyRptCurrencyID'] = $customerReceivePaymentMaster->companyRptCurrencyID;
            $input['companyRptCurrencyER'] = $customerReceivePaymentMaster->companyRptCurrencyER;
            $input['payAmountBank'] = $customerReceivePaymentMaster->bankID;
            $input['payAmountSuppTrans'] = $receiveAmountTotTrans;
            $input['payAmountCompLocal'] = $receiveAmountTotLocal;
            $input['payAmountCompRpt'] = $receiveAmountTotRpt;
            $input['invoiceType'] = $customerReceivePaymentMaster->documentType;
            $input['matchInvoice'] = $customerReceivePaymentMaster->matchInvoice;
            $input['matchingAmount'] = 0;

            $input['confirmedYN'] = $customerReceivePaymentMaster->confirmedYN;
            $input['confirmedByEmpID'] = $customerReceivePaymentMaster->confirmedByEmpID;
            $input['confirmedByEmpSystemID'] = $customerReceivePaymentMaster->confirmedByEmpSystemID;
            $input['confirmedByName'] = $customerReceivePaymentMaster->confirmedByName;
            $input['confirmedDate'] = $customerReceivePaymentMaster->confirmedDate;
            $input['approved'] = $customerReceivePaymentMaster->approved;
            $input['approvedDate'] = $customerReceivePaymentMaster->approvedDate;
        } else if ($input['matchType'] == 2) {

            if($input['isAutoCreateDocument']){
                $creditNoteDetails = CreditNoteDetails::where('creditNoteAutoID', $input['custReceivePaymentAutoID'])->first();
                if (empty($creditNoteDetails)) {
                    return [
                        'status' => false,
                        'message' => "Credit Note Details not found",
                        'type' => []
                    ];
                }
    
                $creditNoteMaster = CreditNote::find($input['custReceivePaymentAutoID']);
                if (empty($creditNoteMaster)) {
                    return [
                        'status' => false,
                        'message' => "Credit Note not found",
                        'type' => []
                    ];
                }
            } else {
                $creditNoteDetails = CreditNoteDetails::where('creditNoteDetailsID', $input['custReceivePaymentAutoID'])->first();
                if (empty($creditNoteDetails)) {
                    return [
                        'status' => false,
                        'message' => "Credit Note Details not found",
                        'type' => []
                    ];
                }
    
                $creditNoteMaster = CreditNote::find($creditNoteDetails->creditNoteAutoID);
                if (empty($creditNoteMaster)) {
                    return [
                        'status' => false,
                        'message' => "Credit Note not found",
                        'type' => []
                    ];
                }
            }


            $existCheck = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                ->where('PayMasterAutoId', $creditNoteDetails->creditNoteAutoID)
                ->where('matchingConfirmedYN', 0)
                ->where('documentSystemID', $creditNoteMaster->documentSystemiD)
                ->where('serviceLineSystemID', $creditNoteDetails->serviceLineSystemID)
                ->first();

            if($existCheck){
                return [
                    'status' => false,
                    'message' => "A matching document for the selected credit note is created and not confirmed. Please confirm the previously created document and try again.",
                    'type' => []
                ];
            }

            if(!isset($input['isAutoCreateDocument'])){
                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')->where('documentSystemID', $creditNoteMaster->documentSystemiD)->where('companySystemID', $creditNoteMaster->companySystemID)->where('documentSystemCode', $creditNoteDetails->creditNoteAutoID)->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')->first();
                if ($glCheck) {
                    if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                        return [
                            'status' => false,
                            'message' => "Selected credit note is not updated in general ledger. Please check again",
                            'type' => []
                        ];
                    }
                } else {
                    return [
                        'status' => false,
                        'message' => "Selected credit note is not updated in general ledger. Please check again",
                        'type' => []
                    ];
                }
            }
            $customerDetail = CustomerMaster::find($creditNoteMaster->customerID);
            $input['matchingType'] = 'AR';

            if($input['isAutoCreateDocument']){
                $input['PayMasterAutoId'] = $input['custReceivePaymentAutoID'];
            } else {
                $input['PayMasterAutoId'] = $creditNoteDetails->creditNoteAutoID;
            }

            $input['serviceLineSystemID'] = $creditNoteDetails->serviceLineSystemID;
            $input['documentSystemID'] = $creditNoteMaster->documentSystemiD;
            $input['documentID'] = $creditNoteMaster->documentID;
            $input['BPVcode'] = $creditNoteMaster->creditNoteCode;
            $input['BPVdate'] = $creditNoteMaster->creditNoteDate;
            if($input['isAutoCreateDocument']){  
                $input['BPVNarration'] = $input['narration'];
            } else {
                $input['BPVNarration'] = $creditNoteMaster->comments;
            }
            //$input['directPaymentPayeeSelectEmp'] =  $customerDetail->CustomerName;
            $input['directPaymentPayee'] = $customerDetail->CustomerName;
            $input['directPayeeCurrency'] = $creditNoteMaster->customerCurrencyID;
            $input['BPVsupplierID'] = $creditNoteMaster->customerID;
            $input['supplierGLCodeSystemID'] = $creditNoteMaster->customerGLCodeSystemID;
            $input['supplierGLCode'] = $creditNoteMaster->customerGLCode;
            $input['supplierTransCurrencyID'] = $creditNoteMaster->customerCurrencyID;
            $input['supplierTransCurrencyER'] = $creditNoteMaster->customerCurrencyER;
            $input['supplierDefCurrencyID'] = $creditNoteMaster->customerCurrencyID;
            $input['supplierDefCurrencyER'] = $creditNoteMaster->customerCurrencyER;
            $input['localCurrencyID'] = $creditNoteMaster->localCurrencyID;
            $input['localCurrencyER'] = $creditNoteMaster->localCurrencyER;
            $input['companyRptCurrencyID'] = $creditNoteMaster->companyReportingCurrencyID;
            $input['companyRptCurrencyER'] = $creditNoteMaster->companyReportingER;
            //$input['payAmountBank'] = $creditNoteMaster->payAmountBank;
            $input['payAmountSuppTrans'] = $creditNoteDetails->creditAmount;
            //$input['payAmountSuppDef'] = $creditNoteMaster->debitAmountTrans;
            //$input['suppAmountDocTotal'] = $creditNoteMaster->suppAmountDocTotal;
            $input['payAmountCompLocal'] = $creditNoteDetails->localAmount;
            $input['payAmountCompRpt'] = $creditNoteDetails->comRptAmount;
            $input['invoiceType'] = $creditNoteMaster->documentType;
            $input['matchingAmount'] = 0;
            $input['confirmedYN'] = $creditNoteMaster->confirmedYN;
            $input['confirmedByEmpID'] = $creditNoteMaster->confirmedByEmpID;
            $input['confirmedByEmpSystemID'] = $creditNoteMaster->confirmedByEmpSystemID;
            $input['confirmedByName'] = $creditNoteMaster->confirmedByName;
            $input['confirmedDate'] = $creditNoteMaster->confirmedDate;
            $input['approved'] = $creditNoteMaster->approved;
            $input['approvedDate'] = $creditNoteMaster->approvedDate;
        }
        $input['matchingDocCode'] = 0;
        if($input['isAutoCreateDocument']){
            $input['matchingDocdate'] = \Carbon\Carbon::parse($input['matchingDocdate'])->format('Y-m-d H:i:s');
        } else {
            $input['matchingDocdate'] = date('Y-m-d H:i:s');
        }

        $input['createdPcID'] = gethostname();
        
        if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
            $employee = UserTypeService::getSystemEmployee();
            $input['createdUserID'] = $employee->empID;
            $input['createdUserSystemID'] = $employee->employeeSystemID;
        }
        else{
            $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
            $input['createdUserID'] = \Helper::getEmployeeID();
        }


        $currentFinanceYear = \Helper::companyFinanceYear($input['companySystemID'], 0);


        if(isset($currentFinanceYear) && count($currentFinanceYear) > 0)
        {
               
            $companyfinanceyear = CompanyFinanceYear::select('bigginingDate','endingDate')->where('companyFinanceYearID', $currentFinanceYear[0]->companyFinanceYearID)
            ->where('companySystemID', $input['companySystemID'])
            ->first();
                if ($companyfinanceyear) {
                    $input['companyFinanceYearID'] = $currentFinanceYear[0]->companyFinanceYearID;

                    $companyFinancePeriod = CompanyFinancePeriod::select('companyFinancePeriodID')->where('companySystemID', '=', $input['companySystemID'])
                    ->where('companyFinanceYearID', $currentFinanceYear[0]->companyFinanceYearID)
                    ->where('departmentSystemID', 1)
                    ->where('isActive', -1)
                    ->where('isCurrent', -1)
                    ->first();

                    if($companyFinancePeriod)
                    {
                        $input['companyFinancePeriodID'] = $companyFinancePeriod->companyFinancePeriodID;
                    }

                }
        }
        $matchDocumentMasters = MatchDocumentMaster::create($input);
        if($input['isAutoCreateDocument']){
            return [
                'status' => true,
                'data' => $matchDocumentMasters->refresh()->toArray(),
                'message' => 'Receipt Matching saved successfully'
            ];
        } else {
            return $matchDocumentMasters;
        }


    }

    private static function areAllElementsSame($array) {
        $collection = collect($array);
        return $collection->every(function ($value) use ($collection) {
            return $value === $collection->first();
        });
    }

    public static function createReceiptMatchingDetails($input)
    {
        $master = MatchDocumentMaster::with('transactioncurrency', 'localcurrency', 'rptcurrency')->find($input['matchDocumentMasterAutoID']);
        $arLedger = AccountsReceivableLedger::find($input['arAutoID']);
       
        if (!$master || !$arLedger) {
            return ['status' => false, 'message' => "Required master or invoice data not found."];
        }

        //Find the source document (Invoice, Direct Invoice, or Credit Note) to get all details from.
        $sourceDocument = null;
        if ($arLedger->documentSystemID == 2) { // 2 = Customer Invoice
            $sourceDocument = CustomerInvoice::where('custInvoiceDirectAutoID', $arLedger->documentCodeSystem)->first();
        } elseif ($arLedger->documentSystemID == 21) { // 21 = Credit Note
            $sourceDocument = CreditNote::where('creditNoteAutoID', $arLedger->documentCodeSystem)->first();
        } elseif ($arLedger->documentSystemID == 20) { // 20 = Customer Invoice Direct
            $sourceDocument = CustomerInvoiceDirect::where('custInvoiceDirectAutoID', $arLedger->documentCodeSystem)->first();
        }

        if (!$sourceDocument) {
            throw new Exception("Could not find the source document for AR Ledger entry: " . $arLedger->arAutoID . ". System ID: " . $arLedger->documentSystemID . ", Code System: " . $arLedger->documentCodeSystem);
        }

        $detail = new CustomerReceivePaymentDetail();

        // IDs and Codes
        $detail->matchingDocID = $input['matchDocumentMasterAutoID'];
        $detail->custReceivePaymentAutoID = $master->PayMasterAutoId;
        $detail->arAutoID = $arLedger->arAutoID;
        $detail->bookingInvCodeSystem = $arLedger->documentCodeSystem;
        $detail->addedDocumentSystemID = $arLedger->documentSystemID;
        $detail->bookingInvCode = $arLedger->documentCode;
        $detail->addedDocumentID = $arLedger->documentID;
        $detail->companySystemID = $arLedger->companySystemID;
        $detail->companyID = $arLedger->companyID;
        $detail->bookingDate = $arLedger->documentDate;
        
        // Service Line from the actual source document
        $detail->serviceLineSystemID = $arLedger->serviceLineSystemID;
        $detail->serviceLineCode = $arLedger->serviceLineCode;

        // Currency and Exchange Rates from the master matching document
        $detail->custTransactionCurrencyID = $master->supplierTransCurrencyID;
        $detail->localCurrencyID = $master->localCurrencyID;
        $detail->companyReportingCurrencyID = $master->companyRptCurrencyID;
        $detail->custReceiveCurrencyID = $master->supplierTransCurrencyID;
        $detail->custTransactionCurrencyER = $master->supplierTransCurrencyER;
        $detail->localCurrencyER = $master->localCurrencyER;
        $detail->companyReportingER = $master->companyRptCurrencyER;
        $detail->custReceiveCurrencyER = $master->supplierTransCurrencyER;

        // Amounts
        $detail->bookingAmountTrans = $arLedger->custInvoiceAmount;
        $detail->bookingAmountLocal = $arLedger->localAmount;
        $detail->bookingAmountRpt = $arLedger->comRptAmount;
        $detail->custbalanceAmount = $arLedger->custInvoiceAmount - $input['receiveAmountTrans'];
        $detail->receiveAmountTrans = $input['receiveAmountTrans'];

        // Use the helper to calculate local/rpt amounts for the received value
        $conversionAmount = Helper::convertAmountToLocalRpt(205, $detail->matchingDocID, $detail->receiveAmountTrans);
        $detail->receiveAmountLocal = Helper::roundValue($conversionAmount["localAmount"]);
        $detail->receiveAmountRpt = Helper::roundValue($conversionAmount["reportingAmount"]);

        // VAT Information from the source document
        if (($arLedger->documentSystemID == 2 || $arLedger->documentSystemID == 20) && isset($sourceDocument->vatRegisteredYN) && $sourceDocument->vatRegisteredYN) {
            $details = null;
            if (isset($sourceDocument->isPerforma) && ($sourceDocument->isPerforma == 1 || $sourceDocument->isPerforma == 0)) {
                $details = CustomerInvoiceDirectDetail::where('custInvoiceDirectID', $sourceDocument->custInvoiceDirectAutoID)->get();
            } else {
                $details = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID', $sourceDocument->custInvoiceDirectAutoID)->get();
            }

            if ($details && !$details->isEmpty()) {
                $allValuesAreTheSame = self::areAllElementsSame($details->pluck('vatSubCategoryID'));

                if ($details->count() == 1 || $allValuesAreTheSame) {
                    $det = $details->first();
                    $detail->vatSubCategoryID = $det->vatSubCategoryID;
                    $detail->vatMasterCategoryID = $det->vatMasterCategoryID;
                    $detail->VATPercentage = $det->VATPercentage;
                    $detail->isVatDisabled = 1;
                } else {
                    $defaultVAT = TaxVatCategories::whereHas('tax', function ($q) use ($master) {
                        $q->where('companySystemID', $master->companySystemID)
                            ->where('isActive', 1)
                            ->where('taxCategory', 2);
                    })
                    ->whereHas('main', function ($q) {
                        $q->where('isActive', 1);
                    })
                    ->where('isActive', 1)
                    ->where('isDefault', 1)
                    ->first();

                    if ($defaultVAT) {
                        $detail->vatSubCategoryID = $defaultVAT->taxVatSubCategoriesAutoID;
                        $detail->vatMasterCategoryID = $defaultVAT->mainCategory;
                        $detail->VATPercentage = $defaultVAT->percentage;
                    } else {
                        throw new Exception("Default VAT not configured");
                    }
                }
            } else {
                // if no details found, fallback to header values for categories and percentage.
                $detail->vatMasterCategoryID = $sourceDocument->vatMasterCategoryID;
                $detail->vatSubCategoryID = $sourceDocument->vatSubCategoryID;
                $detail->VATPercentage = $sourceDocument->VATPercentage ?? 0;
                $detail->isVatDisabled = $sourceDocument->isVatDisabled ?? 0;
            }
            
            // Now, calculate the VAT amounts based on the determined percentage and the received amount.
            if (isset($detail->VATPercentage) && $detail->VATPercentage > 0) {
                 $vatAmount = ($detail->receiveAmountTrans * $detail->VATPercentage) / (100 + $detail->VATPercentage);
                 $detail->VATAmount = Helper::roundValue($vatAmount);
                 $conversionAmount = Helper::convertAmountToLocalRpt(205, $detail->matchingDocID, $vatAmount);
                 $detail->VATAmountLocal = Helper::roundValue($conversionAmount["localAmount"]);
                 $detail->VATAmountRpt = Helper::roundValue($conversionAmount["reportingAmount"]);
            } else {
                 $detail->VATAmount = 0;
                 $detail->VATAmountRpt = 0;
                 $detail->VATAmountLocal = 0;
            }

        } else {
            // Not a CustomerInvoice/Direct or not VAT registered, use existing logic from source document header
            $detail->VATAmount = $sourceDocument->vatAmount ?? 0;
            $detail->VATAmountRpt = $sourceDocument->vatAmountRpt ?? 0;
            $detail->VATAmountLocal = $sourceDocument->vatAmountLocal ?? 0;
            $detail->VATPercentage = $sourceDocument->vatPercentage ?? 0;
            $detail->vatMasterCategoryID = $sourceDocument->vatMasterCategoryID;
            $detail->vatSubCategoryID = $sourceDocument->vatSubCategoryID;
            $detail->isVatDisabled = $sourceDocument->isVatDisabled ?? 0;
        }

        // Other fields
        $detail->comments = $master->comments;


        if (!$detail->save()) {
            throw new Exception("Failed to save receipt matching detail for invoice " . $detail->bookingInvCode);
        }

        return ['status' => true, 'message' => "Receipt matching detail created successfully"];
    }



    public static function updateReceiptMatching($input,$isAutoCreateDocument = false)
    {


        $id = $input['matchDocumentMasterAutoID'];

        /** @var MatchDocumentMaster $matchDocumentMaster */
        $matchDocumentMaster = MatchDocumentMaster::find($id);
        
        if (empty($matchDocumentMaster)) {
            return ['status' => false, 'message' => "Match Document Master not found"];
        }

        $supplierCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($matchDocumentMaster->supplierTransCurrencyID);

        if (isset($input['matchingDocdate'])) {
            if ($input['matchingDocdate']) {
                $input['matchingDocdate'] = new Carbon($input['matchingDocdate']);
            }
        }

        $customValidation = CustomValidation::validation(70, $matchDocumentMaster, 2, $input);
        if (!$customValidation["success"]) {
            return [
                'status' => false,
                'message' => $customValidation["message"],
                'type' => ['already_confirmed']
            ];

        }

        $detailAmountTotTran = CustomerReceivePaymentDetail::where('matchingDocID', $id)
            ->sum('receiveAmountTrans');

        $detailAmountTotLoc = CustomerReceivePaymentDetail::where('matchingDocID', $id)
            ->sum('receiveAmountLocal');

        $detailAmountTotRpt = CustomerReceivePaymentDetail::where('matchingDocID', $id)
            ->sum('receiveAmountRpt');

        $input['matchingAmount'] = $detailAmountTotTran;
        $input['matchedAmount'] = $detailAmountTotTran;
        $input['matchLocalAmount'] = \Helper::roundValue($detailAmountTotLoc);
        $input['matchRptAmount'] = \Helper::roundValue($detailAmountTotRpt);


        //checking below posted data
        if ($input['documentSystemID'] == 21) {

            $CustomerReceivePaymentDataUpdateCHK = CustomerReceivePayment::find($input['PayMasterAutoId']);

            $postedDate = date("Y-m-d", strtotime($CustomerReceivePaymentDataUpdateCHK->postedDate));

            $formattedMatchingDate = date("Y-m-d", strtotime($input['matchingDocdate']));

            if ($formattedMatchingDate < $postedDate) {
                return [
                    'status' => false,
                    'message' => 'Receipt voucher is posted on ' . $postedDate . '. You cannot select a date less than posted date !',
                    'type' => ['posted_date']
                ];
            }

        } elseif ($input['documentSystemID'] == 19) {

            $creditNoteDataUpdateCHK = CreditNote::find($input['PayMasterAutoId']);
            if (empty($creditNoteDataUpdateCHK)) {
                return [
                    'status' => false,
                    'message' => 'Credit Note not found',
                    'type' => ['credit_note_not_found']
                ];
            }

            $postedDate = date("Y-m-d", strtotime($creditNoteDataUpdateCHK->postedDate));

            $formattedMatchingDate = date("Y-m-d", strtotime($input['matchingDocdate']));

            if ($formattedMatchingDate < $postedDate) {
                return [
                    'status' => false,
                    'message' => 'Credit note is posted on ' . $postedDate . '. You cannot select a date less than posted date !',
                    'type' => ['posted_date']
                ];
            }
        }
        if($isAutoCreateDocument){
            $input['matchingConfirmedYN'] = 1;
        }
        if ($matchDocumentMaster->matchingConfirmedYN == 0 && $input['matchingConfirmedYN'] == 1) {

            $pvDetailExist = CustomerReceivePaymentDetail::select(DB::raw('matchingDocID,addedDocumentSystemID'))
                ->where('matchingDocID', $id)
                ->first();

            if (empty($pvDetailExist)) {
                return [
                    'status' => false,
                    'message' => 'Matching document cannot confirm without details',
                    'type' => ['confirm']
                ];
            }

            $currencyValidate = CurrencyValidation::validateCurrency("receipt_matching", $matchDocumentMaster);
            if (!$currencyValidate['status']) {
                return [
                    'status' => false,
                    'message' => $currencyValidate['message'],
                    'type' => ['confirm']
                ];
            }

            $detailAllRecords = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                ->get();

            if ($detailAllRecords) {
                foreach ($detailAllRecords as $row) {
                    if ($row['addedDocumentSystemID'] == 20) {
                        $checkAmount = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                            ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                            ->where('receiveAmountTrans', '<=', 0)
                            ->count();

                        if ($checkAmount > 0) {
                            return [
                                'status' => false,
                                'message' => 'Matching amount cannot be 0',
                                'type' => ['confirm']
                            ];
                        }
                    } elseif ($row['addedDocumentSystemID'] == 19) {
                        $checkAmount = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                            ->where('addedDocumentSystemID', $row['addedDocumentSystemID'])
                            ->where('receiveAmountTrans', '=', 0)
                            ->count();

                        if ($checkAmount > 0) {
                            return [
                                'status' => false,
                                'message' => 'Matching amount cannot be 0',
                                'type' => ['confirm']
                            ];
                        }
                    }
                }
            }

            $detailAmountTotTran = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                ->sum('receiveAmountTrans');

            if (round($detailAmountTotTran, $supplierCurrencyDecimalPlace) > round($input['matchBalanceAmount'], $supplierCurrencyDecimalPlace)) {
                return [
                    'status' => false,
                    'message' => 'Detail amount cannot be greater than balance amount to match',
                    'type' => ['confirm']
                ];
            }

            if ($input['matchingDocCode'] == 0) {

                $company = Company::find($input['companySystemID']);

                $lastSerial = MatchDocumentMaster::where('companySystemID', $input['companySystemID'])
                    ->where('matchDocumentMasterAutoID', '<>', $input['matchDocumentMasterAutoID'])
                    ->where('matchingType', 'AR')
                    ->orderBy('serialNo', 'desc')
                    ->first();

                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serialNo) + 1;
                }

                $matchingDocCode = ($company->CompanyID . '\\' . 'MT' . str_pad($lastSerialNumber, 8, '0', STR_PAD_LEFT));

                $input['serialNo'] = $lastSerialNumber;
                $input['matchingDocCode'] = $matchingDocCode;
            }
            $itemExistArray = array();

            foreach ($detailAllRecords as $item) {

                $payDetailMoreBooked = CustomerReceivePaymentDetail::selectRaw('IFNULL(SUM(IFNULL(receiveAmountTrans,0)),0) as receiveAmountTrans')
                    ->where('arAutoID', $item['arAutoID'])
                    ->first();

                $a = $payDetailMoreBooked->receiveAmountTrans;
                $b = $item['bookingAmountTrans'];
                $epsilon = 0.00001;
                if(($a-$b) > $epsilon) {
                    $itemDrt = "Selected invoice " . $item['bookingInvCode'] . " booked more than the invoice amount.";
                    $itemExistArray[] = [$itemDrt];

                }
            }

            if (!empty($itemExistArray)) {
                    return [
                    'status' => false,
                    'message' => $itemExistArray,
                    'type' => ['confirm']
                ];
            }

            foreach ($detailAllRecords as $val) {

                $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $val['arAutoID'])
                    ->sum('receiveAmountTrans');

                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
                    ->where('companySystemID', $val["companySystemID"])
                    ->where('PayMasterAutoId', $val["bookingInvCodeSystem"])
                    ->where('documentSystemID', $val["addedDocumentSystemID"])
                    ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')
                    ->first();

                if(!$matchedAmount){
                    $matchedAmount['SumOfmatchedAmount'] = 0;
                }

                $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];

                $arLedgerUpdate = AccountsReceivableLedger::find($val['arAutoID']);

                if ($val['addedDocumentSystemID'] == 20) {
                    if ($totReceiveAmount == 0) {
                        $arLedgerUpdate->fullyInvoiced = 0;
                        $arLedgerUpdate->selectedToPaymentInv = 0;
                    } else if (($val->bookingAmountTrans == $totReceiveAmount) || ($totReceiveAmount > $val->bookingAmountTrans)) {
                        $arLedgerUpdate->fullyInvoiced = 2;
                        $arLedgerUpdate->selectedToPaymentInv = -1;
                    } else if (($val->bookingAmountTrans > $totReceiveAmount) && ($totReceiveAmount > 0)) {
                        $arLedgerUpdate->fullyInvoiced = 1;
                        $arLedgerUpdate->selectedToPaymentInv = 0;
                    }
                } else if ($val['addedDocumentSystemID'] == 19) {
                    if ($totReceiveAmount == 0) {
                        $arLedgerUpdate->fullyInvoiced = 0;
                        $arLedgerUpdate->selectedToPaymentInv = 0;
                    } else if (($val->bookingAmountTrans == $totReceiveAmount) || ($totReceiveAmount < $val->bookingAmountTrans)) {
                        $arLedgerUpdate->fullyInvoiced = 2;
                        $arLedgerUpdate->selectedToPaymentInv = -1;
                    } else if (($val->bookingAmountTrans < $totReceiveAmount) && ($totReceiveAmount < 0)) {
                        $arLedgerUpdate->fullyInvoiced = 1;
                        $arLedgerUpdate->selectedToPaymentInv = 0;
                    }
                }

                $arLedgerUpdate->save();
            }


            //updating master table
            if ($input['documentSystemID'] == 21) {

                $CustomerReceivePaymentDataUpdate = CustomerReceivePayment::find($input['PayMasterAutoId']);

                $customerSettleAmountSum = CustomerReceivePaymentDetail::selectRaw('erp_custreceivepaymentdet.bookingAmountTrans, 
                                                        addedDocumentSystemID, 
                                                        bookingInvCodeSystem, 
                                                        Sum(erp_custreceivepaymentdet.receiveAmountTrans) AS SumDetailAmount')
                                            ->where('custReceivePaymentAutoID', $input['PayMasterAutoId'])
                                            ->where('bookingInvCode', '0')
                                            ->groupBy('custReceivePaymentAutoID')
                                            ->first();


                $directDetails = DirectReceiptDetail::selectRaw("SUM(localAmount) as SumDetailAmountLocal, 
                                                                    SUM(comRptAmount) as SumDetailAmountRpt,
                                                                    SUM(DRAmount) as SumDetailAmountTrans")
                                                        ->where('directReceiptAutoID', $input['PayMasterAutoId'])
                                                        ->groupBy('directReceiptAutoID')
                                                        ->first();

                $advReceiptDetails = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as SumDetailAmountLocal, 
                                                                        SUM(comRptAmount) as SumDetailAmountRpt,
                                                                        SUM(paymentAmount) as SumAdvDetailAmountTrans")
                                                            ->where('custReceivePaymentAutoID', $input['PayMasterAutoId'])
                                                            ->groupBy('custReceivePaymentAutoID')
                                                            ->first();

                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentSystemID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')
                                                    ->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)
                                                    ->where('documentSystemID', $matchDocumentMaster->documentSystemID)
                                                    ->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')
                                                    ->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }
                $receiveAmountTot = 0;
                if ($customerSettleAmountSum) {
                    $receiveAmountTot = $customerSettleAmountSum["SumDetailAmount"];
                }

                if($directDetails){
                    $receiveAmountTot += $directDetails["SumDetailAmountTrans"];
                }

                if($advReceiptDetails){
                    $receiveAmountTot += $advReceiptDetails["SumAdvDetailAmountTrans"];
                }

                $RoundedMachAmount = round($machAmount, $supplierCurrencyDecimalPlace);
                $RoundedReceiveAmountTot = round($receiveAmountTot, $supplierCurrencyDecimalPlace);

                if ($machAmount == 0) {
                    $CustomerReceivePaymentDataUpdate->matchInvoice = 0;
                } else if ($RoundedReceiveAmountTot == $RoundedMachAmount || $RoundedMachAmount > $RoundedReceiveAmountTot) {
                    $CustomerReceivePaymentDataUpdate->matchInvoice = 2;
                } else if ($RoundedReceiveAmountTot > $RoundedMachAmount && $RoundedMachAmount > 0) {
                    $CustomerReceivePaymentDataUpdate->matchInvoice = 1;
                }
                $CustomerReceivePaymentDataUpdate->save();
            }
            if ($input['documentSystemID'] == 19) {

                $creditNoteDataUpdate = CreditNote::find($input['PayMasterAutoId']);
                if (empty($creditNoteDataUpdate)) {
                    return [
                        'status' => false,
                        'message' => 'Credit Note not found',
                        'type' => ['credit_note_not_found']
                    ];
                }

                //when adding a new matching, checking whether debit amount more than the document value
                $customerSettleAmountSum = CustomerReceivePaymentDetail::selectRaw('erp_custreceivepaymentdet.bookingAmountTrans, addedDocumentSystemID, bookingInvCodeSystem, companySystemID, Sum(erp_custreceivepaymentdet.receiveAmountTrans) AS SumDetailAmount')
                    ->where('addedDocumentSystemID', $creditNoteDataUpdate->documentSystemiD)
                    ->where('bookingInvCodeSystem', $creditNoteDataUpdate->creditNoteAutoID)
                    ->groupBy('addedDocumentSystemID', 'bookingInvCodeSystem')
                    ->first();


                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')
                    ->where('PayMasterAutoId', $matchDocumentMaster->PayMasterAutoId)
                    ->where('documentSystemID', $matchDocumentMaster->documentSystemID)
                    ->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')
                    ->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }

                $customerDetailSum = 0;
                if ($customerSettleAmountSum) {
                    $customerDetailSum = abs($customerSettleAmountSum["SumDetailAmount"]);
                }

                $totalPaidAmount = ($customerDetailSum + $machAmount);
                $RoundedTotalPaidAmount = round($totalPaidAmount, $supplierCurrencyDecimalPlace);
                $RoundedCreditAmountTrans = round($creditNoteDataUpdate->creditAmountTrans, $supplierCurrencyDecimalPlace);

                if ($totalPaidAmount == 0) {
                    $creditNoteDataUpdate->matchInvoice = 0;
                } elseif ($RoundedCreditAmountTrans == $RoundedTotalPaidAmount) {
                    $creditNoteDataUpdate->matchInvoice = 2;
                } elseif ($RoundedTotalPaidAmount > $RoundedCreditAmountTrans) {
                    $creditNoteDataUpdate->matchInvoice = 2;
                } elseif ($RoundedCreditAmountTrans > $RoundedTotalPaidAmount && ($RoundedTotalPaidAmount > 0)) {
                    $creditNoteDataUpdate->matchInvoice = 1;
                }
                $creditNoteDataUpdate->save();
            }


            $input['matchingConfirmedYN'] = 1;
            if($isAutoCreateDocument){
                $systemEmployee = UserTypeService::getSystemEmployee();
                $input['matchingConfirmedByEmpSystemID'] = $systemEmployee->employeeSystemID;
                $input['matchingConfirmedByEmpID'] = $systemEmployee->empID;
                $input['matchingConfirmedByName'] = $systemEmployee->empName;   
            } else {
                $input['matchingConfirmedByEmpSystemID'] = \Helper::getEmployeeSystemID();
                $input['matchingConfirmedByEmpID'] = \Helper::getEmployeeID();
                $input['matchingConfirmedByName'] = \Helper::getEmployeeName();
            }

            $input['matchingConfirmedDate'] = \Helper::currentDateTime();

            $data = [];
            $taxLedgerData = [];
            $finalData = [];

            $validatePostedDate = GlPostedDateService::validatePostedDate($input['PayMasterAutoId'], $input["documentSystemID"]);

            if (!$validatePostedDate['status']) {
                    return [
                    'status' => false,
                    'message' => $validatePostedDate['message'],
                    'type' => ['posted_date']
                ];
            }

            $masterDocumentDate =  $validatePostedDate['postedDate'];

            $matchDocumentMaster->update($input);

            $matchDocumentMaster = MatchDocumentMaster::with('segment')->find($input['matchDocumentMasterAutoID']);

            if ($input['documentSystemID'] == 21)
            {
                $masterData = CustomerReceivePayment::with(['bank', 'finance_period_by'])->find($input['PayMasterAutoId']);

                $data['companySystemID'] = $matchDocumentMaster->companySystemID;
                $data['companyID'] = $matchDocumentMaster->companyID;
                $data['serviceLineSystemID'] = null;
                $data['serviceLineCode'] = null;
                $data['masterCompanyID'] = null;
                $data['documentSystemID'] = $matchDocumentMaster->documentSystemID;
                $data['documentID'] = $matchDocumentMaster->documentID;
                $data['documentSystemCode'] = $input["PayMasterAutoId"];
                $data['documentCode'] = $masterData->custPaymentReceiveCode;
                $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                $data['documentConfirmedDate'] = $matchDocumentMaster->matchingConfirmedDate;
                $data['documentConfirmedBy'] = $matchDocumentMaster->confirmedByEmpID;
                $data['documentConfirmedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                $data['documentFinalApprovedDate'] = $matchDocumentMaster->approvedDate;
                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                $data['documentFinalApprovedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                $data['documentNarration'] = "Matching Entry ".$matchDocumentMaster->matchingDocCode;
                $data['clientContractID'] = 'X';
                $data['contractUID'] = 159;
                $data['supplierCodeSystem'] = $masterData->customerID;
                $data['holdingShareholder'] = null;
                $data['holdingPercentage'] = 0;
                $data['nonHoldingPercentage'] = 0;
                $data['chequeNumber'] = $masterData->custChequeNo;
                $data['documentType'] = $masterData->documentType;
                $data['createdDateTime'] = \Helper::currentDateTime();

                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    $employee = UserTypeService::getSystemEmployee();
                    $input['createdUserID'] = $employee->empID;
                    $input['createdUserSystemID'] = $employee->employeeSystemID;
                }
                else{
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                }
                
                $data['createdUserPC'] = gethostname();
                $data['timestamp'] = \Helper::currentDateTime();
                $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                $directReceipts = DirectReceiptDetail::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode, SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt")
                                                        ->WHERE('directReceiptAutoID', $input['PayMasterAutoId'])
                                                        ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID')
                                                        ->get();

                $advReceipts = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount, SUM(paymentAmount) as transAmount, localCurrencyID as localCurrencyID, comRptCurrencyID as reportingCurrencyID,customerTransCurrencyID as transCurrencyID, comRptER as reportingCurrencyER, localER, customerTransER as transCurrencyER,serviceLineSystemID,serviceLineCode, SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt")
                                                        ->WHERE('custReceivePaymentAutoID', $input["PayMasterAutoId"])
                                                        ->groupBy('serviceLineSystemID')
                                                        ->get();



                        foreach ($directReceipts as $directReceipt)
                        {
                            if($matchDocumentMaster->serviceLineSystemID == $directReceipt->serviceLineSystemID) {
                                foreach ($detailAllRecords as $detailRecord) {
                                    $data['serviceLineSystemID'] = $directReceipt->serviceLineSystemID;
                                    $data['serviceLineCode'] = $directReceipt->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $masterData->custAdvanceAccountSystemID;
                                    $data['glCode'] = $masterData->custAdvanceAccount;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentTransAmount'] =  Helper::roundValue(abs($detailRecord->receiveAmountTrans));
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->localCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->localCurrencyER);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->companyRptCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->companyRptCurrencyER);
                                    $data['timestamp'] = Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }

                        foreach ($directReceipts as $directReceipt)
                        {
                            if($matchDocumentMaster->serviceLineSystemID == $directReceipt->serviceLineSystemID) {
                                foreach ($detailAllRecords as $detailRecord) {
                                    $data['serviceLineSystemID'] = $detailRecord->serviceLineSystemID != null ? $detailRecord->serviceLineSystemID : $directReceipt->serviceLineSystemID;
                                    $data['serviceLineCode'] = $detailRecord->serviceLineCode != null ? $detailRecord->serviceLineCode : $directReceipt->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                                    $data['glCode'] = $masterData->customerGLCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $detailRecord->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $detailRecord->custTransactionCurrencyER;
                                    $data['documentTransAmount'] =  Helper::roundValue($detailRecord->receiveAmountTrans) * -1;
                                    $data['documentLocalCurrencyID'] = $detailRecord->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $detailRecord->localCurrencyER;
                                    $data['documentLocalAmount'] = Helper::roundValue($detailRecord->receiveAmountLocal) * -1;
                                    $data['documentRptCurrencyID'] = $detailRecord->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $detailRecord->companyReportingER;
                                    $data['documentRptAmount'] = Helper::roundValue($detailRecord->receiveAmountRpt) * -1;
                                    $data['timestamp'] = Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }




                        foreach ($advReceipts as $advReceipt) {
                            if($matchDocumentMaster->serviceLineSystemID == $advReceipt->serviceLineSystemID) {
                                foreach ($detailAllRecords as $detailRecord) {
                                    $data['serviceLineSystemID'] = $advReceipt->serviceLineSystemID;
                                    $data['serviceLineCode'] = $advReceipt->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $masterData->custAdvanceAccountSystemID;
                                    $data['glCode'] = $masterData->custAdvanceAccount;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentTransAmount'] =  Helper::roundValue(abs($detailRecord->receiveAmountTrans));
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->localCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->localCurrencyER);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = Helper::conversionCurrencyByER($masterData->custTransactionCurrencyID,$masterData->companyRptCurrencyID,abs($detailRecord->receiveAmountTrans),$masterData->companyRptCurrencyER);
                                    $data['timestamp'] = Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }




                        foreach ($advReceipts as $advReceipt)
                        {
                            if($matchDocumentMaster->serviceLineSystemID == $advReceipt->serviceLineSystemID) {
                                foreach ($detailAllRecords as $detailRecord) {
                                    $data['serviceLineSystemID'] = $detailRecord->serviceLineSystemID != null ? $detailRecord->serviceLineSystemID : $advReceipt->serviceLineSystemID;
                                    $data['serviceLineCode'] = $detailRecord->serviceLineCode != null ? $detailRecord->serviceLineCode : $advReceipt->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                                    $data['glCode'] = $masterData->customerGLCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $detailRecord->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $detailRecord->custTransactionCurrencyER;
                                    $data['documentTransAmount'] =  \Helper::roundValue($detailRecord->receiveAmountTrans) * -1;
                                    $data['documentLocalCurrencyID'] = $detailRecord->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $detailRecord->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($detailRecord->receiveAmountLocal) * -1;
                                    $data['documentRptCurrencyID'] = $detailRecord->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $detailRecord->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue($detailRecord->receiveAmountRpt) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        
                        if ($masterData->isVATApplicable == 1 && $masterData->documentType == 15) {

                            if(isset($input['validInvoice']) && $input['validInvoice'])
                            {    
                                $detailAllRecordsObj = CustomerReceivePaymentDetail::where('matchingDocID', $id)
                                ->with('reciept_vocuher')->get();
    
                                    foreach($detailAllRecordsObj as $records)
                                    {
                                        if(isset($records->reciept_vocuher->VATAmount) && $records->reciept_vocuher->VATAmount == 0)
                                        {
                                            return [    
                                                'status' => false,
                                                'message' => 'Invoice without VAT is being matched with reciept with VAT.This will nullify the VAT entries to zero.Are you sure you want to proceed ?',
                                                'type' => ['UnconfirmAsset']
                                            ];
    
                                        }
    
                                    }   
                            }
                    


                            $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, 
                                                        SUM(rptAmount) as rptAmount,
                                                        SUM(amount) as transAmount,
                                                        localCurrencyID,
                                                        rptCurrencyID as reportingCurrencyID,
                                                        currency as supplierTransactionCurrencyID,
                                                        currencyER as supplierTransactionER,
                                                        rptCurrencyER as companyReportingER,
                                                        localCurrencyER")
                                                        ->WHERE('documentSystemCode', $input["PayMasterAutoId"])
                                                        ->WHERE('documentSystemID', $input["documentSystemID"])
                                                        ->groupBy('documentSystemCode')
                                                        ->first();
                                $taxLedgerData = [];

                                $customerMatchingDetails = CustomerReceivePaymentDetail::with(['ar_data'])->selectRaw("SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt,arAutoID")
                                                    ->where('custReceivePaymentAutoID', $input["PayMasterAutoId"])
                                                    ->get();

                                $taxConfigData = TaxService::getOutputVATGLAccount($input["companySystemID"]);

                                if (!empty($taxConfigData)) {  // out put vat entries
                                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                                        ->where('companySystemID', $input["companySystemID"])
                                        ->first();
                
                                    if (!empty($chartOfAccountData)) {
                                        $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        $data['glCode'] = $chartOfAccountData->AccountCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $taxLedgerData['outputVatGLAccountID'] = $data['chartOfAccountSystemID'];
                                    } else {
                                        Log::info('Receipt voucher VAT GL Entry Issues Id :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Receipt voucher VAT GL Entry IssuesId :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                                }

                                $data['clientContractID'] = 'X';
                                $data['contractUID'] = 159;
                                
                                if($tax)
                                {   
                                    $data['documentTransCurrencyID'] = $tax->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $tax->supplierTransactionER;
                                    $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                    $data['documentRptCurrencyID'] = $tax->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $tax->companyReportingER;
                                }
                                else
                                {
                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                }


                                foreach ($customerMatchingDetails as $key => $value) {
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($value->VATAmount)) ;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($value->VATAmountLocal)) ;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($value->VATAmountRpt)) ;
                                    $data['serviceLineSystemID'] = $matchDocumentMaster->segment->serviceLineSystemID;
                                    $data['serviceLineCode'] = $matchDocumentMaster->segment->ServiceLineCode;
                                    array_push($finalData, $data);
                                }

                
                                $taxConfigData = TaxService::getOutputVATTransferGLAccount($input["companySystemID"]);
            
                                if (!empty($taxConfigData)) {
                                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatTransferGLAccountAutoID)
                                        ->where('companySystemID', $masterData->companySystemID)
                                        ->first();
            
                                    if (!empty($chartOfAccountData)) {
                                        $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        $data['glCode'] = $chartOfAccountData->AccountCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
            
                                        $taxLedgerData['outputVatTransferGLAccountID'] = $data['chartOfAccountSystemID'];
                                    } else {
                                        Log::info('Receipt voucher VAT GL Entry Issues Id :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Receipt voucher VAT GL Entry IssuesId :' . $input["PayMasterAutoId"] . ', date :' . date('H:i:s'));
                                    Log::info('Output VAT transfer GL Account not configured' . date('H:i:s'));
                                }
                                foreach ($customerMatchingDetails as $key => $value) {
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($value->VATAmount)) * -1;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($value->VATAmountLocal)) * -1;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($value->VATAmountRpt)) * -1;
                                    $data['serviceLineSystemID'] = $matchDocumentMaster->segment->serviceLineSystemID;
                                    $data['serviceLineCode'] = $matchDocumentMaster->segment->ServiceLineCode;
                                    array_push($finalData, $data);
                                }
                               
                                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                                    $employee = UserTypeService::getSystemEmployee();
                                    $employeeSystemID = $employee->employeeSystemID;
                                }
                                else{
                                    $employeeSystemID= \Helper::getEmployeeSystemID();
                                }
                                if (count($taxLedgerData) > 0) {
                                    $masterModel = [
                                        'employeeSystemID' => $employeeSystemID,
                                        'documentSystemID' => $matchDocumentMaster->documentSystemID,
                                        'matchDocumentMasterAutoID' => $matchDocumentMaster->matchDocumentMasterAutoID,
                                        'autoID' => $input['PayMasterAutoId'],
                                        'matching' => true,
                                        'companySystemID' => $matchDocumentMaster->companySystemID,
                                        'documentDate' => $matchDocumentMaster->matchingDocdate
                                    ];

                                    $taxResponse = RecieptVoucherTaxLedgerService::processEntry($taxLedgerData, $masterModel);

                                    if ($taxResponse['status']) {
                                        $finalDataTax = $taxResponse['data']['finalData'];
                                        $finalDetailDataTax = $taxResponse['data']['finalDetailData'];


                                        if ($finalDataTax) {
                                            foreach ($finalDataTax as $tempFinalDataTax)
                                            {
                                                TaxLedger::create($tempFinalDataTax);
                                            }

                                            foreach ($finalDetailDataTax as $tempFinalDetailDataTax)
                                            {
                                                TaxLedgerDetail::create($tempFinalDetailDataTax);
                                            }
                                        }
                                    } 
                                }

                        }

                        $tempCollection = collect($finalData);
                        $finalLocalAmount = $tempCollection->sum('documentLocalAmount') * -1;
                        $finalRptAmount = $tempCollection->sum('documentRptAmount') * -1;

                        $epsilon = 0.00001;

                        if((abs($finalLocalAmount) > $epsilon) || (abs($finalRptAmount) > $epsilon)) {
                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                            $data['documentTransAmount'] = 0;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] =\Helper::roundValue($finalLocalAmount);
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($finalRptAmount);
                            $data['timestamp'] = \Helper::currentDateTime();
                            if(isset($advReceipt)) {
                                $data['serviceLineSystemID'] = $advReceipt->serviceLineSystemID;
                                $data['serviceLineCode'] = $advReceipt->serviceLineCode;
                            }
                            elseif (isset($directReceipt)) {
                                $data['serviceLineSystemID'] = $directReceipt->serviceLineSystemID;
                                $data['serviceLineCode'] = $directReceipt->serviceLineCode;
                            }
                            else {
                                $data['serviceLineSystemID'] = '';
                                $data['serviceLineCode'] = '';
                            }
                            array_push($finalData, $data);
                        }
            }

            if ($input['documentSystemID'] == 19) {

                $creditNoteMasterData = CreditNote::with('details')->find($input['PayMasterAutoId']);

                $data['companySystemID'] = $matchDocumentMaster->companySystemID;
                $data['companyID'] = $matchDocumentMaster->companyID;
                $data['serviceLineSystemID'] = null;
                $data['serviceLineCode'] = null;
                $data['masterCompanyID'] = null;
                $data['documentSystemID'] = $matchDocumentMaster->documentSystemID;
                $data['documentID'] = $matchDocumentMaster->documentID;
                $data['documentSystemCode'] = $input["PayMasterAutoId"];
                $data['documentCode'] = $creditNoteMasterData->creditNoteCode;
                $data['documentDate'] = $matchDocumentMaster->matchingDocdate;
                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                $data['documentConfirmedDate'] = $matchDocumentMaster->matchingConfirmedDate;
                $data['documentConfirmedBy'] = $matchDocumentMaster->confirmedByEmpID;
                $data['documentConfirmedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                $data['documentFinalApprovedDate'] = $matchDocumentMaster->approvedDate;
                $data['documentFinalApprovedBy'] = $creditNoteMasterData->approvedByUserID;
                $data['documentFinalApprovedByEmpSystemID'] = $matchDocumentMaster->confirmedByEmpSystemID;
                $data['documentNarration'] = "Matching Entry ".$matchDocumentMaster->matchingDocCode;
                $data['clientContractID'] = 'X';
                $data['contractUID'] = 159;
                $data['supplierCodeSystem'] = $creditNoteMasterData->customerID;
                $data['holdingShareholder'] = null;
                $data['holdingPercentage'] = 0;
                $data['nonHoldingPercentage'] = 0;
                $data['chequeNumber'] = 0;
                $data['documentType'] = $creditNoteMasterData->documentType;
                $data['createdDateTime'] = \Helper::currentDateTime();

                if(isset($input['isAutoCreateDocument']) && $input['isAutoCreateDocument']){
                    $employee = UserTypeService::getSystemEmployee();
                    $input['createdUserID'] = $employee->empID;
                    $input['createdUserSystemID'] = $employee->employeeSystemID;
                }
                else{
                    $data['createdUserID'] = \Helper::getEmployeeID();
                    $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                }
                $data['createdUserPC'] = gethostname();
                $data['timestamp'] = \Helper::currentDateTime();
                $data['matchDocumentMasterAutoID'] = $matchDocumentMaster->matchDocumentMasterAutoID;

                $gainLocalAmount = $gainRptAmount = 0;

                foreach ($detailAllRecords as $row) {

                    $tempValue = $row['receiveAmountLocal'] - Helper::conversionCurrencyByER(1,2,$row['receiveAmountTrans'],$creditNoteMasterData['localCurrencyER']);
                    $gainLocalAmount += round($tempValue,5);


                    $tempValue = $row['receiveAmountRpt'] - Helper::conversionCurrencyByER(1,2,$row['receiveAmountTrans'],$creditNoteMasterData['companyReportingER']);
                    $gainRptAmount += round($tempValue,5);
                }

                if(($gainLocalAmount != 0) || ($gainRptAmount != 0)) {
                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($creditNoteMasterData->companySystemID, $creditNoteMasterData->documentSystemiD, "exchange-gainloss-gl");
                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($creditNoteMasterData->companySystemID, $creditNoteMasterData->documentSystemiD, "exchange-gainloss-gl");
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $creditNoteMasterData->customerCurrencyID;
                    $data['documentTransCurrencyER'] = $creditNoteMasterData->customerCurrencyER;
                    $data['documentTransAmount'] = 0;
                    $data['documentLocalCurrencyID'] = $creditNoteMasterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $creditNoteMasterData->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue($gainLocalAmount);
                    $data['documentRptCurrencyID'] = $creditNoteMasterData->companyReportingCurrencyID;
                    $data['documentRptCurrencyER'] = $creditNoteMasterData->companyReportingER;
                    $data['documentRptAmount'] = \Helper::roundValue($gainRptAmount);
                    $data['timestamp'] = \Helper::currentDateTime();
                    $data['serviceLineSystemID'] = $creditNoteMasterData->details->first()->serviceLineSystemID;
                    $data['serviceLineCode'] = $creditNoteMasterData->details->first()->serviceLineCode;
                    array_push($finalData, $data);

                    $data['chartOfAccountSystemID'] = $creditNoteMasterData->customerGLCodeSystemID;
                    $data['glCode'] = $creditNoteMasterData->customerGLCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $creditNoteMasterData->customerCurrencyID;
                    $data['documentTransCurrencyER'] = $creditNoteMasterData->customerCurrencyER;
                    $data['documentTransAmount'] = 0;
                    $data['documentLocalCurrencyID'] = $creditNoteMasterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $creditNoteMasterData->localCurrencyER;
                    if($gainLocalAmount < 0) {
                        $data['documentLocalAmount'] = \Helper::roundValue(abs($gainLocalAmount));
                    }
                    else {
                        $data['documentLocalAmount'] = \Helper::roundValue($gainLocalAmount) * -1;
                    }
                    $data['documentRptCurrencyID'] = $creditNoteMasterData->companyReportingCurrencyID;
                    $data['documentRptCurrencyER'] = $creditNoteMasterData->companyReportingER;
                    if($gainRptAmount < 0) {
                        $data['documentRptAmount'] = \Helper::roundValue(abs($gainRptAmount));
                    }
                    else {
                        $data['documentRptAmount'] = \Helper::roundValue($gainRptAmount) * -1;
                    }
                    $data['timestamp'] = \Helper::currentDateTime();
                    $data['serviceLineSystemID'] = $creditNoteMasterData->details->first()->serviceLineSystemID;
                    $data['serviceLineCode'] = $creditNoteMasterData->details->first()->serviceLineCode;
                    array_push($finalData, $data);
                }
            }

            foreach ($finalData as $storeData) {
                GeneralLedger::create($storeData);
            }
        }

        $input['modifiedPc'] = gethostname();
        if($isAutoCreateDocument){
            $systemEmployee = UserTypeService::getSystemEmployee();
            $input['modifiedUser'] = $systemEmployee->empID;
            $input['modifiedUserSystemID'] = $systemEmployee->employeeSystemID;
        } else {
            $input['modifiedUser'] = \Helper::getEmployeeID();
            $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        }

        

        $matchDocumentMaster->update($input);
        return [
            'status' => true,
            'message' => "Receipt matching updated successfully",
            'data' => $matchDocumentMaster
        ];
    }
}