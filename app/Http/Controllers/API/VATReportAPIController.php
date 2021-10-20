<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateYearAPIRequest;
use App\Http\Requests\API\UpdateYearAPIRequest;
use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\DocumentMaster;
use App\Models\GeneralLedger;
use App\Models\GRVMaster;
use App\Models\PurchaseReturn;
use App\Models\SupplierAssigned;
use App\Models\Tax;
use App\Models\TaxLedger;
use App\Models\TaxVatMainCategories;
use App\Models\TaxVatCategories;
use App\Models\Year;
use App\Repositories\YearRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

class VATReportAPIController extends AppBaseController
{

    public function __construct()
    {
    }

    public function getVATFilterFormData(Request$request){
        $selectedCompanyId = $request['selectedCompanyId'];
/*
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $companies = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup',0)->get();*/

        $listOfDocuments = [3,8,12,13,10,24,61,4,11,15,19,20,21,17,41,71,87,7,22,23 ];
        $documentTypes = DocumentMaster::whereIn('documentSystemID',$listOfDocuments)->get();

        $vatTypes = TaxVatCategories::where('isActive', 1)
                                   ->whereHas('tax', function($query) use ($selectedCompanyId) {
                                        $query->where('companySystemID', $selectedCompanyId);
                                   })
                                   ->get();

        $suppliers = SupplierAssigned::where('companySystemID', $selectedCompanyId)->get();
        $customers = CustomerAssigned::where('companySystemID', $selectedCompanyId)->get();

        $output = array(
            'documentTypes' => $documentTypes,
            'vatTypes' => $vatTypes,
            'customers' => $customers,
            'suppliers' => $suppliers,
        );
        return  $this->sendResponse($output,'Data retrieved successfully');
    }

    public function validateVATReport(Request $request){

        $reportTypeID = $request->reportTypeID;

        switch ($reportTypeID) {
            case 1:
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'currencyID' => 'required',
                    'documentTypes' => 'required',
//                    'vatTypes' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;

            case 2:
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'currencyID' => 'required',
                    'documentTypes' => 'required',
                    //                   'vatTypes' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 3:
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;

            default:
                return $this->sendError('No report ID found');
        }
        return $this->sendResponse([],'Data Retrieved Successfully');
    }

    public function generateVATReport(Request $request){
        $input = $request->all();
        $input = $input['filterData'];

        if($input['reportTypeID'] == 3){
            $output = $this->getVatReturnFillingReport($input);
        }else{
            $output = $this->getVatReportQuery($input);
        }

        $search = $request->input('search.value');
        /*if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $output = $output->where(function ($query) use ($search) {
                $query->where('documentID', 'LIKE', "%{$search}%")
                    ->orWhere('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere(function($q) use($search){
                        $q->whereIn('documentSystemID',[3,24,11,15,4])
                            ->whereHas('supplier', function ($q1) use($search){
                                $q1->where('supplierName','LIKE',"%{$search}%");
                            });
                    })
                    ->orWhere(function($q) use($search){
                        $q->whereIn('documentSystemID',[19,20,21,71])
                            ->whereHas('customer', function ($q1) use($search){
                                $q1->where('CustomerName','LIKE',"%{$search}%");
                            });
                    });
            });
        };*/


        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('orderCondition', 'desc')
            ->make(true);

    }

    private function getAmountDetailsFromDocuments($row){
        $output= [
            'localAmount'=> 0,
            'rptAmount'=> 0
        ];
        $localDecimal = isset($row->localcurrency->DecimalPlaces)?$row->localcurrency->DecimalPlaces:3;
        $rptDecimal = isset($row->rptcurrency->DecimalPlaces)?$row->rptcurrency->DecimalPlaces:2;
        switch ($row->documentSystemID){
            case 3: //GRV
                $output= [
                    'localAmount'=> isset($row->grv->grvTotalLocalCurrency)?number_format($row->grv->grvTotalLocalCurrency,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->grv->grvTotalComRptCurrency)?number_format($row->grv->grvTotalComRptCurrency,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            /*case 8: // Material Issue
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            /*case 12:
                // SR - Material Return
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            /*case 13:    // ST - Stock Transfer
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            /*case 10: // RS - Stock Receive
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            /*case 61:
                // INRC - Inventory Reclassififcation
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            case 24:// PRN - Purchase Return
                $output= [
                    'localAmount'=> isset($row->purchase_return->totalLocalAmount)?number_format($row->purchase_return->totalLocalAmount,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->purchase_return->totalComRptAmount)?number_format($row->purchase_return->totalComRptAmount,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            case 20: /*customer Invoice*/
                $output= [
                    'localAmount'=> isset($row->customer_invoice->bookingAmountLocal)?number_format($row->customer_invoice->bookingAmountLocal+$row->customer_invoice->VATAmountLocal,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->customer_invoice->bookingAmountRpt)?number_format($row->customer_invoice->bookingAmountRpt+$row->customer_invoice->VATAmountRpt,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            /*case 7: // SA - Stock Adjustment
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            case 11:// SI - Supplier Invoice
                $output= [
                    'localAmount'=> isset($row->supplier_invoice->bookingAmountLocal)?number_format($row->supplier_invoice->bookingAmountLocal,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->supplier_invoice->bookingAmountRpt)?number_format($row->supplier_invoice->bookingAmountRpt,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            case 15:// Debit note
                $output= [
                    'localAmount'=> isset($row->debit_note->debitAmountLocal)?number_format($row->debit_note->debitAmountLocal,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->debit_note->debitAmountRpt)?number_format($row->debit_note->debitAmountRpt,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            case 19:// Credit note
                $output= [
                    'localAmount'=> isset($row->credit_note->creditAmountLocal)?number_format($row->credit_note->creditAmountLocal,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->credit_note->creditAmountRpt)?number_format($row->credit_note->creditAmountRpt,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            case 4:// PV - Payment Voucher
                $output= [
                    'localAmount'=> isset($row->payment_voucher->payAmountCompLocal)?number_format($row->payment_voucher->payAmountCompLocal,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->payment_voucher->payAmountCompRpt)?number_format($row->payment_voucher->payAmountCompRpt,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            case 21:// BRV - Customer Receive Payment
                $output= [
                    'localAmount'=> isset($row->bank_receipt->localAmount)?number_format($row->bank_receipt->localAmount,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->bank_receipt->companyRptAmount)?number_format($row->bank_receipt->companyRptAmount,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            /*case 17:// JV - Journal Voucher
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            /*case 22:// FA - Fixed Asset Master
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;
            case 23:// FAD - Fixed Asset Depreciation
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;
            case 41:// FADS - Fixed Asset Disposal
                $output= [
                    'localAmount'=> 0,
                    'rptAmount'=> 0
                ];
                break;*/
            case 71:/*Delivery Order*/

                $output= [
                    'localAmount'=> isset($row->delivery_order->companyLocalAmount)?number_format($row->delivery_order->companyLocalAmount,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->delivery_order->companyReportingAmount)?number_format($row->delivery_order->companyReportingAmount,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            case 87: // sales return
                $currencyConversionAmount = \Helper::currencyConversion($row->sales_return->companySystemID, $row->sales_return->transactionCurrencyID, $row->sales_return->transactionCurrencyID, $row->sales_return->transactionAmount);

                $output= [
                    'localAmount'=> isset($currencyConversionAmount['localAmount'])?number_format($currencyConversionAmount['localAmount'],$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($currencyConversionAmount['reportingAmount'])?number_format($currencyConversionAmount['reportingAmount'],$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            default:
                $output = [];
        }
        return $output;
    }

    public function exportVATReport(Request $request){
        $input = $request->all();

        if($input['reportTypeID'] == 3){
            $output = $this->exportVATReturnFillingReport($input);
        }

        $output = $this->getVatReportQuery($input);

        if (count((array)$output)>0) {
            $x = 0;
            $data = [];
            foreach ($output as $val) {
                $x++;

                $data[$x]['Document Type'] = $val->document_master->documentID;
                $data[$x]['Document Code'] = $val->documentCode;
                $data[$x]['Document Date'] = Helper::dateFormat($val->documentDate);
                if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                    $data[$x]['Party Name'] =isset($val->supplier->supplierName) ? $val->supplier->supplierName: '';
                }elseif (in_array($val->documentSystemID, [19, 20, 21, 71])){
                    $data[$x]['Party Name'] =isset($val->customer->CustomerName) ? $val->customer->CustomerName: '';
                }else{
                    $data[$x]['Party Name'] ='';
                }

                $data[$x]['Approved By'] = isset($val->final_approved_by->empName)? $val->final_approved_by->empName : '';

                $localDecimalPlaces = isset($val->localcurrency->DecimalPlaces) ? $val->localcurrency->DecimalPlaces : 3;
                $rptDecimalPlaces = isset($val->rptcurrency->DecimalPlaces) ? $val->rptcurrency->DecimalPlaces : 2;

                $data[$x]['Document Total Amount'] = round($val->documentLocalAmount,$localDecimalPlaces);
                $data[$x]['Document VAT Amount'] = round($val->localAmount,$localDecimalPlaces);
                if(isset($input['currencyID'])&&$input['currencyID']==2){
                    $data[$x]['Document Total Amount'] = round($val->documentReportingAmount,$rptDecimalPlaces);
                    $data[$x]['Document VAT Amount'] = round($val->rptAmount,$rptDecimalPlaces);
                }

                $data[$x]['VAT Main Category'] = isset($val->main_category->mainCategoryDescription) ? $val->main_category->mainCategoryDescription : '-';
                $data[$x]['VAT Type'] = isset($val->sub_category->subCategoryDescription) ? $val->sub_category->subCategoryDescription : '-';
                $data[$x]['Is Claimed'] = ($val->isClaimed == 1) ? 'Claimed' : "Not Claimed";
            }

            \Excel::create('vat_report', function ($excel) use ($data) {
                $excel->sheet('sheet name', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                });
                $lastrow = $excel->getActiveSheet()->getHighestRow();
                $excel->getActiveSheet()->getStyle('A1:N' . $lastrow)->getAlignment()->setWrapText(true);
            })->download('csv');

            return $this->sendResponse(array(), 'successfully export');
        }
        return $this->sendError( 'No Records Found');
    }

    public function exportVATReturnFillingReport($input){

        $output = $this->getVatReturnFillingReport($input);

        if (count((array)$output)>0) {
            $x = 0;
            $data = [];
            foreach ($output as $val) {
                $x++;


                $data[$x]['purchaseOrderCode'] = isset($val->purchaseOrderCode)? $val->purchaseOrderCode : '';
                $data[$x]['supplierID'] = isset($val->supplierID)? $val->supplierID : '';
                $data[$x]['supplierPrimaryCode'] = isset($val->supplierPrimaryCode)? $val->supplierPrimaryCode : '';
                $data[$x]['supplierName'] = isset($val->supplierName)? $val->supplierName : '';
                $data[$x]['supplierTransactionCurrencyID'] = isset($val->supplier_currency_code)? $val->supplier_currency_code : '';
                $data[$x]['unitCost'] = isset($val->unitCost)? $val->unitCost : '';
                $data[$x]['VATAmountLocal'] = isset($val->VATAmountLocal)? $val->VATAmountLocal : '';
                $data[$x]['netAmount'] = isset($val->netAmount)? $val->netAmount : '';
                $data[$x]['poCurrency'] = isset($val->poCurrency)? $val->poCurrency : '';
                $data[$x]['RCMStatus'] = isset($val->RCMStatus)? $val->RCMStatus : '';
                $data[$x]['poConfirmedDate'] = isset($val->poConfirmedDate)? Helper::dateFormat($val->poConfirmedDate) : '';
                $data[$x]['poApproveStatus'] = isset($val->poApproveStatus)? $val->poApproveStatus : '';
                $data[$x]['approvedDate'] = isset($val->poApproveStatus)? Helper::dateFormat($val->approvedDate) : '';
                $data[$x]['totalLogisticAmount'] = isset($val->totalLogisticAmount)? $val->totalLogisticAmount : '';
                $data[$x]['totalLogisticVATAmount'] = isset($val->totalLogisticVATAmount)? $val->totalLogisticVATAmount : '';
                $data[$x]['addVatOnPO'] = isset($val->addVatOnPO)? $val->addVatOnPO : '';
                $data[$x]['itemPrimaryCode'] = isset($val->itemPrimaryCode)? $val->itemPrimaryCode : '';
                $data[$x]['itemDescription'] = isset($val->itemDescription)? $val->itemDescription : '';
                $data[$x]['supplierPartNumber'] = isset($val->supplierPartNumber)? $val->supplierPartNumber : '';
                $data[$x]['unitShortCode'] = isset($val->unitShortCode)? $val->unitShortCode : '';
                $data[$x]['receivedStatus'] = isset($val->receivedStatus)? $val->receivedStatus : '';
                $data[$x]['qtyToReceive'] = isset($val->qtyToReceive)? $val->qtyToReceive : '';
                $data[$x]['noQty'] = isset($val->noQty)? $val->noQty : '';
                $data[$x]['qtyReceived'] = isset($val->qtyReceived)? $val->qtyReceived : '';
                $data[$x]['VATAmount'] = isset($val->VATAmount)? $val->VATAmount : '';
                $data[$x]['GRVcostPerUnitComRptCur'] = isset($val->GRVcostPerUnitComRptCur)? $val->GRVcostPerUnitComRptCur : '';
                $data[$x]['total'] = isset($val->total)? $val->total : '';
                $data[$x]['vatSubCategory'] = isset($val->vatSubCategory)? $val->vatSubCategory : '';
                $data[$x]['grvPrimaryCode'] = isset($val->grvPrimaryCode)? $val->grvPrimaryCode : '';
                $data[$x]['lastOfgrvDate'] = isset($val->lastOfgrvDate)? Helper::dateFormat($val->lastOfgrvDate) : '';
                $data[$x]['grvQty'] = isset($val->grvQty)? $val->grvQty : '';
                $data[$x]['grvQty'] = isset($val->grvQty)? $val->grvQty : '';
                $data[$x]['grvNetAmount'] = isset($val->grvNetAmount)? $val->grvNetAmount : '';
                $data[$x]['bookingInvCode'] = isset($val->bookingInvCode)? $val->bookingInvCode : '';
                $data[$x]['secondaryRefNo'] = isset($val->secondaryRefNo)? $val->secondaryRefNo : '';
                //$data[$x]['approvedDate'] = isset($val->approvedDate)? $val->approvedDate : '';
                $data[$x]['lastOfInvoiceDate'] = isset($val->lastOfInvoiceDate)? Helper::dateFormat($val->lastOfInvoiceDate) : '';
                $data[$x]['invoiceTotalAmount'] = isset($val->invoiceTotalAmount)? $val->invoiceTotalAmount : '';
                $data[$x]['invoiceVatAmount'] = isset($val->invoiceVatAmount)? $val->invoiceVatAmount : '';

            }

            \Excel::create('vat_report', function ($excel) use ($data) {
                $excel->sheet('sheet name', function ($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true);
                    $sheet->setAutoSize(true);
                    $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                });
                $lastrow = $excel->getActiveSheet()->getHighestRow();
                $excel->getActiveSheet()->getStyle('A1:N' . $lastrow)->getAlignment()->setWrapText(true);
            })->download('csv');

            return $this->sendResponse(array(), 'successfully export');
        }
        return $this->sendError( 'No Records Found');
    }

    private function getVatReportQuery($input,$isForDataTable=0){

        $documentSystemIDs = [];
        $vatTypesIDs = [];
        $companySystemID = $input['companySystemID'];
        $currencyID = $input['currencyID'];
        $fromDate = null;
        $toDate = null;
        $reportTypeID = $input['reportTypeID'];

        if(isset($input['documentTypes'])){
            $documentTypes = (array)$input['documentTypes'];
            $documentSystemIDs = array_filter(collect($documentTypes)->pluck('documentSystemID')->toArray());
        }
        if(isset($input['vatTypes'])){
            $vatTypes = (array)$input['vatTypes'];
            $vatTypesIDs = array_filter(collect($vatTypes)->pluck('taxVatSubCategoriesAutoID')->toArray());
        }
        if(isset($input['fromDate'])){
            $fromDate = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $toDate = new Carbon($input['toDate']);
        }

        $accountTypeIds = [];
        if (isset($input['accountType'])) {
            $accountTypeIds = array_filter(collect($input['accountType'])->pluck('id')->toArray());
        }

        $output = TaxLedger::where('companySystemID',$companySystemID)
                           ->whereDate('documentDate','>=',$fromDate)
                           ->whereDate('documentDate','<=',$toDate)
                           ->when(count($documentSystemIDs) > 0, function ($query) use ($documentSystemIDs) {
                                $query->whereIn('documentSystemID',$documentSystemIDs);
                            })
                            ->when(count($vatTypesIDs) > 0, function ($query) use ($vatTypesIDs) {
                                $query->whereIn('subCategoryID',$vatTypesIDs);
                            })
                            ->when($reportTypeID == 1, function ($query) use ($accountTypeIds) {
                                if (sizeof($accountTypeIds) == 1) {
                                    $query->when($accountTypeIds[0] == 1, function ($query) {
                                                $query->whereNotNull('outputVatTransferGLAccountID');
                                          })
                                          ->when($accountTypeIds[0] == 2, function ($query) {
                                                $query->whereNotNull('outputVatGLAccountID');
                                          });
                                } else {
                                    $query->where(function ($query) {
                                                $query->whereNotNull('outputVatTransferGLAccountID')
                                                      ->orWhereNotNull('outputVatGLAccountID');
                                            });
                                }
                            })
                            ->when($reportTypeID == 2, function ($query) use ($accountTypeIds) {
                                 if (sizeof($accountTypeIds) == 1) {
                                    $query->when($accountTypeIds[0] == 1, function ($query) {
                                                $query->whereNotNull('inputVatTransferAccountID');
                                          })
                                          ->when($accountTypeIds[0] == 2,function ($query) {
                                                $query->whereNotNull('inputVATGlAccountID');
                                          });
                                } else {
                                    $query->where(function ($query) {
                                                $query->whereNotNull('inputVatTransferAccountID')
                                                      ->orWhereNotNull('inputVATGlAccountID');
                                            });
                                }
                            })
                            ->when(isset($input['isClaimed']), function ($query) use ($input) {
                                $query->where('isClaimed', $input['isClaimed']);
                            })
                            ->with(['supplier','customer','rptcurrency','localcurrency','final_approved_by','document_master','main_category', 'sub_category'])
                            ->orderBy('taxLedgerID', 'desc');

        if($isForDataTable==0){
            $output = $output->get();
        }

        return $output;

    }

    private function getVatReturnFillingReport($input)
    {
        $companyId = $input['companySystemID'];
        $fromDate = null;
        $toDate = null;

        $isGroup = \Helper::checkIsCompanyGroup($companyId);
        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $childCompanies = [$companyId];
        }

        if(isset($input['fromDate'])){
            $fromDate = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $toDate = new Carbon($input['toDate']);
        }

        $sql = "SELECT erp_purchaseorderdetails.purchaseOrderMasterID, erp_purchaseorderdetails.purchaseOrderDetailsID,
            podet.purchaseOrderCode, podet.supplierID, podet.supplierPrimaryCode, podet.supplierName, podet.supplierTransactionCurrencyID,
            podet.supplier_currency_code, podet.poCurrency, podet.RCMStatus, podet.poConfirmedDate, podet.poApproveStatus,
            podet.approvedDate, logistic.totalLogisticAmount, logistic.totalLogisticVATAmount,
            IF ( logistic.addVatOnPO > 0, 'YES', 'NO' ) AS addVatOnPO,
            erp_purchaseorderdetails.itemCode, erp_purchaseorderdetails.itemPrimaryCode, erp_purchaseorderdetails.itemDescription,
            erp_purchaseorderdetails.supplierPartNumber, erp_purchaseorderdetails.unitOfMeasure, units.UnitShortCode AS unitShortCode,
            IF (
                (
                    IF( podet.manuallyClosed = 1, IFNULL( gdet.noQty, 0 ), 
                        IFNULL( erp_purchaseorderdetails.noQty, 0 ) 
                    ) - gdet.noQty 
                ) = 0,
                'Fully Received',
                IF ( ISNULL( gdet.noQty ) OR gdet.noQty = 0, 'Not Received', 'Partially Received' ) 
            ) AS receivedStatus,
            IF( podet.manuallyClosed = 1, 0, ( IFNULL( ( erp_purchaseorderdetails.noQty - gdet.noQty ), 0 ) ) ) AS qtyToReceive,
            IF( podet.manuallyClosed = 1, IFNULL( gdet.noQty, 0 ), IFNULL( erp_purchaseorderdetails.noQty, 0 ) ) AS noQty,
            IFNULL( gdet.noQty, 0 ) AS qtyReceived, erp_purchaseorderdetails.VATAmount,
            erp_purchaseorderdetails.GRVcostPerUnitComRptCur,
            (
                IF( podet.manuallyClosed = 1, IFNULL( gdet.noQty, 0 ), 
                    IFNULL( erp_purchaseorderdetails.noQty, 0 ) 
                ) * erp_purchaseorderdetails.GRVcostPerUnitComRptCur 
            ) AS total,
            erp_tax_vat_sub_categories.subCategoryDescription AS vatSubCategory, gdet2.grvAutoID, gdet2.grvPrimaryCode,
            gdet2.lastOfgrvDate, gdet2.grvQty, gdet2.grvNetAmount, lastInvoice.bookingSupInvoiceDetAutoID,
            lastInvoice.bookingSuppMasInvAutoID, lastInvoice.bookingInvCode, lastInvoice.lastOfInvoiceDate,
            lastInvoice.invoiceTotalAmount, lastInvoice.invoiceVatAmount 
            FROM erp_purchaseorderdetails
            INNER JOIN (
                SELECT locationName, manuallyClosed, ServiceLineDes AS segment, purchaseOrderID,
                erp_purchaseordermaster.companyID, locationName AS location, approved, YEAR ( approvedDate ) AS postingYear,
                approvedDate AS orderDate, erp_purchaseordermaster.createdDateTime, purchaseOrderCode,
                erp_purchaseordermaster.supplierTransactionCurrencyID, currencymaster.CurrencyCode AS supplier_currency_code,
                IF( sentToSupplier = 0, 'Not Released', 'Released' ) AS STATUS, supplierID, supplierPrimaryCode,
                supplierName, creditPeriod, deliveryTerms, paymentTerms, expectedDeliveryDate, narration,
                poConfirmedDate, approvedDate, erp_purchaseordermaster.companySystemID, supCont.countryName,
                IFNULL( suppliercategoryicvmaster.categoryDescription, '-' ) AS icvMasterDes,
                IFNULL( suppliercategoryicvsub.categoryDescription, '-' ) AS icvSubDes,
                IF( supCont.isLCCYN = 1, 'YES', 'NO' ) AS isLcc, IF ( supCont.isSMEYN = 1, 'YES', 'NO' ) AS isSme,
                IF( rcmActivated = 1, 'YES', 'NO' ) AS RCMStatus, IF ( approved = - 1, 'YES', 'NO' ) AS poApproveStatus,
                currencymaster.CurrencyCode AS poCurrency 
                FROM erp_purchaseordermaster
                LEFT JOIN serviceline ON erp_purchaseordermaster.serviceLineSystemID = serviceline.serviceLineSystemID
                LEFT JOIN currencymaster ON erp_purchaseordermaster.supplierTransactionCurrencyID = currencymaster.currencyID
                LEFT JOIN suppliercategoryicvmaster ON erp_purchaseordermaster.supCategoryICVMasterID = suppliercategoryicvmaster.supCategoryICVMasterID
                LEFT JOIN suppliercategoryicvsub ON erp_purchaseordermaster.supCategorySubICVID = suppliercategoryicvsub.supCategorySubICVID
                INNER JOIN ( SELECT supplierCodeSystem FROM suppliermaster) supp ON erp_purchaseordermaster.supplierID = supp.supplierCodeSystem
                LEFT JOIN ( 
                    SELECT countrymaster.countryName, supplierCodeSystem, isSMEYN, isLCCYN 
                    FROM suppliermaster 
                    LEFT JOIN countrymaster ON supplierCountryID = countrymaster.countryID 
                ) supCont ON supCont.supplierCodeSystem = erp_purchaseordermaster.supplierID
                LEFT JOIN erp_location ON poLocation = erp_location.locationID 
                WHERE poCancelledYN = 0 AND approved = - 1  AND poType_N <> 5 
                AND ( approvedDate BETWEEN '$fromDate' AND '$toDate' ) 
                AND erp_purchaseordermaster.companySystemID IN  (".implode(',', $childCompanies).")  
            ) AS podet ON `purchaseOrderMasterID` = `podet`.`purchaseOrderID`
            LEFT JOIN `financeitemcategorymaster` ON `itemFinanceCategoryID` = `itemCategoryID`
            LEFT JOIN `erp_tax_vat_sub_categories` ON `vatSubCategoryID` = `taxVatSubCategoriesAutoID`
            LEFT JOIN ( 
                SELECT categoryDescription AS financecategorysub, AccountDescription AS finance_gl_code_pl, AccountCode, itemCategorySubID 
                FROM financeitemcategorysub 
                LEFT JOIN chartofaccounts ON financeGLcodePLSystemID = chartOfAccountSystemID 
            ) AS catSub ON `itemFinanceCategorySubID` = `catSub`.`itemCategorySubID`
            LEFT JOIN `units` ON `unitOfMeasure` = `UnitID`
            LEFT JOIN ( 
                SELECT SUM( noQty ) AS noQty, purchaseOrderDetailsID 
                FROM erp_grvdetails 
                WHERE erp_grvdetails.companySystemID IN (".implode(',', $childCompanies).")  
                GROUP BY purchaseOrderDetailsID 
            ) AS gdet ON `erp_purchaseorderdetails`.`purchaseOrderDetailsID` = `gdet`.`purchaseOrderDetailsID`
            LEFT JOIN (
                SELECT poID, sum( reqAmount ) AS totalLogisticAmount, sum( VATAmount ) AS totalLogisticVATAmount,
                sum( addVatOnPO ) AS addVatOnPO
                FROM erp_purchaseorderadvpayment 
                WHERE poTermID = 0  AND confirmedYN = 1  AND isAdvancePaymentYN = 1  AND approvedYN = - 1 
                GROUP BY poID
            ) logistic ON erp_purchaseorderdetails.purchaseOrderMasterID = logistic.poID
            LEFT JOIN (
                SELECT t.lastOfgrvDate, d.purchaseOrderDetailsID, m.grvPrimaryCode, m.grvAutoID,
                d.itemCode, d.noQty AS grvQty, d.netAmount AS grvNetAmount 
                FROM erp_grvmaster m
                JOIN (
                    SELECT max( erp_grvmaster.grvDate ) AS lastOfgrvDate, erp_grvdetails.itemCode 
                    FROM ( erp_grvmaster INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID ) 
                    WHERE purchaseOrderDetailsID > 0 #and erp_grvdetails.itemCode in( 225, 228)
                    AND erp_grvmaster.companySystemID IN (".implode(',', $childCompanies).")
                    GROUP BY erp_grvdetails.purchaseOrderMastertID, erp_grvdetails.purchaseOrderDetailsID,
                    erp_grvdetails.itemCode 
                ) t ON m.grvDate = t.lastOfgrvDate
                JOIN erp_grvdetails d ON d.grvAutoID = m.grvAutoID 
                AND t.itemCode = d.itemCode 
            ) AS gdet2 ON `erp_purchaseorderdetails`.`purchaseOrderDetailsID` = `gdet2`.`purchaseOrderDetailsID`
            LEFT JOIN (
                SELECT m.bookingSuppMasInvAutoID, d.bookingSupInvoiceDetAutoID, m.bookingInvCode, t.lastOfInvoiceDate,
                d.grvAutoID, d.purchaseOrderID, d.totTransactionAmount AS invoiceTotalAmount, d.VATAmount AS invoiceVatAmount 
                FROM erp_bookinvsuppmaster AS m
                JOIN erp_bookinvsuppdet d ON d.bookingSuppMasInvAutoID = m.bookingSuppMasInvAutoID
                JOIN (
                    SELECT max( erp_bookinvsuppmaster.supplierInvoiceDate ) AS lastOfInvoiceDate, purchaseOrderID
                    FROM ( erp_bookinvsuppmaster 
                        INNER JOIN erp_bookinvsuppdet ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
                    ) 
                    WHERE bookingSupInvoiceDetAutoID > 0 
                    AND erp_bookinvsuppmaster.companySystemID IN (".implode(',', $childCompanies).")
                    GROUP BY erp_bookinvsuppdet.purchaseOrderID 
                ) t ON t.purchaseOrderID = d.purchaseOrderID AND m.supplierInvoiceDate = t.lastOfInvoiceDate                
            ) AS lastInvoice ON erp_purchaseorderdetails.purchaseOrderMasterID = `lastInvoice`.`purchaseOrderID` 
            WHERE erp_purchaseorderdetails.companySystemID IN (".implode(',', $childCompanies).")         
            GROUP BY erp_purchaseorderdetails.purchaseOrderDetailsID, 
            erp_purchaseorderdetails.itemCode,
            gdet2.grvAutoID, 
            lastInvoice.bookingSuppMasInvAutoID
            ORDER BY podet.approvedDate ASC, purchaseOrderDetailsID DESC";

        return DB::select( DB::raw("$sql"));
    }

}
