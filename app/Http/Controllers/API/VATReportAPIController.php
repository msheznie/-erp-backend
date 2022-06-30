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
use App\Models\TaxLedgerDetail;
use App\Models\TaxVatMainCategories;
use App\Models\TaxVatCategories;
use App\Models\Year;
use App\Repositories\YearRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\CreateExcel;
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
                    'fromDate' => 'required',
                    'customers' => 'required',
                    'documentTypes' => 'required',
                    //                   'vatTypes' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 4:
            case 5:
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'suppliers' => 'required',
                    'documentTypes' => 'required',
                    //                   'vatTypes' => 'required'
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
        $output = $this->getVatReportQuery($input);
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

 
    public function generateVATDetailReport(Request $request){
        $input = $request->all();
        
        $input = $input['filterData'];

        if (isset($input['order']) && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $output = $this->getVatDetailReportQuery($input);
        $search = $request->input('search.value');


        if (isset($input['companySystemID'])) {
            $companyData = Company::find($input['companySystemID']);
        } else {
            $companyData = [];
        }
        
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $output = $output->where(function ($query) use ($search) {
                // $query->where('purchaseRequestCode', 'LIKE', "%{$search}%")
                //     ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }


        return  \DataTables::eloquent($output)
                        ->order(function ($query) use ($input) {
                            if (isset($input['order'])) {
                                if ($input['order'][0]['column'] == 0) {
                                    $query->orderBy('id', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->with('companyData', $companyData)
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
        $company = Company::find($request->companySystemID);
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

                if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                    $data[$x]['Country'] =isset($val->supplier->country->countryName) ? $val->supplier->country->countryName: '';
                }elseif (in_array($val->documentSystemID, [19, 20, 21, 71])){
                    $data[$x]['Country'] =isset($val->customer->country->countryName) ? $val->customer->country->countryName: '';
                }else{
                    $data[$x]['Country'] ='';
                }

                if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                    $data[$x]['VATIN'] =isset($val->supplier->vatNumber) ? $val->supplier->vatNumber: '';
                }elseif (in_array($val->documentSystemID, [19, 20, 21, 71])){
                    $data[$x]['VATIN'] =isset($val->customer->vatNumber) ? $val->customer->vatNumber: '';
                }else{
                    $data[$x]['VATIN'] ='';
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

            $company_name = $company->CompanyName;
            $to_date = \Helper::dateFormat($request->toDate);
            $from_date = \Helper::dateFormat($request->fromDate);

            if($request->reportTypeID == 1){
                $fileName = 'Output VAT Summary';
            } elseif($request->reportTypeID == 2){
                $fileName = 'Input VAT Summary';
            } else{
                $fileName = 'VAT Summary Report';
            }

            $path = 'general-ledger/report/vat_report/excel/';
            $basePath = CreateExcel::process($data,$request->type,$fileName,$path,$from_date,$to_date,$company_name);

            if($basePath == '')
            {
                 return $this->sendError('Unable to export excel');
            }
            else
            {
                 return $this->sendResponse($basePath, trans('custom.success_export'));
            }



        }
        return $this->sendError( 'No Records Found');
    }

     public function exportVATDetailReport(Request $request){
        $input = $request->all();
        $output = $this->getVatDetailReportQuery($input)->get();

        if (count((array)$output)>0) {
            $x = 0;
            $data = [];
            foreach ($output as $val) {
                $x++;

                $data[$x]['Company Code in ERP'] = isset($val->company->CompanyID) ? $val->company->CompanyID : "-";
                $data[$x]['Company VAT Registration Number'] = isset($val->company->CompanyID) ? $val->company->CompanyID : "";
                $data[$x]['Company Name'] = isset($val->company->CompanyID) ? $val->company->CompanyID : "";
                $data[$x]['Tax Period '] = $input['fromDate']." - ". $input['toDate'];
                $data[$x]['Accounting Document Number'] = $val->documentNumber ;
                $data[$x]['Accounting Document Date'] = Helper::dateFormat($val->documentDate);
                $data[$x]['Year'] = Carbon::parse($val->documentDate)->format('Y');
                if ($input['reportTypeID'] == 3) {
                    $data[$x]['Revenue GL Code'] = $val->accountCode;
                    $data[$x]['Revenue GL Code Description'] = $val->accountDescription;
                } else if ($input['reportTypeID'] == 4){
                    $data[$x]['Revenue GL Code'] = $val->accountCode;
                    $data[$x]['Revenue GL Code Description'] = $val->accountDescription;
                }

                $data[$x]['Document Currency'] = isset($val->transcurrency->CurrencyCode) ? $val->transcurrency->CurrencyCode : "";
                $data[$x]['Document Type'] = isset($val->document_master->documentDescription) ? $val->document_master->documentDescription : "";
                $data[$x]['Original Document No'] = $val->originalInvoice;
                $data[$x]['Original Document Date'] = Helper::dateFormat($val->originalInvoiceDate);
                if ($input['reportTypeID'] == 4) {
                    $data[$x]['Payment Due Date'] = "";
                }
                $data[$x]['Date Of Supply'] = Helper::dateFormat($val->dateOfSupply);
                $data[$x]['Reference Invoice No'] = "" ;
                $data[$x]['Reference Invoice Date'] = "";
                if ($input['reportTypeID'] == 3) {
                    $data[$x]['Bill To Country'] = isset($val->country->countryName) ? $val->country->countryName : "";
                    if ($val->documentSystemID == 3 || $val->documentSystemID == 24 || $val->documentSystemID == 11 || $val->documentSystemID == 15 || $val->documentSystemID == 4) {
                        $data[$x]['Bill To CustomerName'] = isset($val->supplier->supplierName) ? $val->supplier->supplierName : "";
                    } else if ($val->documentSystemID == 20 || $val->documentSystemID == 19 || $val->documentSystemID == 21 || $val->documentSystemID == 71 || $val->documentSystemID == 87) {
                        $data[$x]['Bill To CustomerName'] = isset($val->customer->CustomerName) ? $val->customer->CustomerName : "";
                    }
                } else if ($input['reportTypeID'] == 4) {
                    $data[$x]['Supplier Country'] = isset($val->country->countryName) ? $val->country->countryName : "";
                    if ($val->documentSystemID == 3 || $val->documentSystemID == 24 || $val->documentSystemID == 11 || $val->documentSystemID == 15 || $val->documentSystemID == 4) {
                        $data[$x]['Supplier Name'] = isset($val->supplier->supplierName) ? $val->supplier->supplierName : "";
                    } else if ($val->documentSystemID == 20 || $val->documentSystemID == 19 || $val->documentSystemID == 21 || $val->documentSystemID == 71 || $val->documentSystemID == 87) {
                        $data[$x]['Supplier Name'] = isset($val->customer->CustomerName) ? $val->customer->CustomerName : "";
                    }
                }
                $data[$x]['Customer Type'] = ($val->partyVATRegisteredYN) ? "Registered" : "Unregistered";
                $data[$x]['VATIN'] = $val->partyVATRegNo;
                $data[$x]['Invoice Line Item No'] = $val->itemCode;
                $data[$x]['Line Item Description'] = $val->itemDescription;
                if (isset($val->company->companyCountry) && ($val->company->companyCountry == $val->countryID)) {
                    $data[$x]['Place Of Supply'] = isset($val->company->country->countryName) ? $val->company->country->countryName : "";
                } else {
                    $data[$x]['Place Of Supply'] = "Outside ".isset($val->company->country->countryName) ? $val->company->country->countryName : "";
                }
                $data[$x]['Tax Code Type'] = "";
                $data[$x]['Tax Code Description'] = isset($val->sub_category->subCategoryDescription) ? $val->sub_category->subCategoryDescription : "";;
                $data[$x]['VAT Rate'] = $val->VATPercentage;
                $data[$x]['Value Excluding VAT In Document Currency'] = round($val->taxableAmount, $val->transcurrency->DecimalPlaces);
                $data[$x]['Vat In Document Currency'] = round($val->VATAmount, $val->transcurrency->DecimalPlaces);
                $data[$x]['Document Currency To Local Currency Rate'] = $val->localER;
                $data[$x]['Value Excluding VAT In Local Currency'] = round($val->taxableAmountLocal, $val->transcurrency->DecimalPlaces);
                $data[$x]['VAT In Local Currency'] = round($val->VATAmountLocal, $val->transcurrency->DecimalPlaces);
                $data[$x]['VAT GL Code'] = isset($val->output_vat->AccountCode) ? $val->output_vat->AccountCode : "";
                $data[$x]['VAT GL Description'] = isset($val->output_vat->AccountDescription) ? $val->output_vat->AccountDescription : "";
                 if ($input['reportTypeID'] == 4) {
                    $data[$x]['Input Tax Recoverability'] = (isset($val->company->vatRegisteredYN) && $val->company->vatRegisteredYN) ? "Yes" : "No";
                    $data[$x]['Input Tax Recoverability %'] = $val->recovertabilityPercentage;
                    $data[$x]['Input Tax Recoverability (Amount)'] = round($val->recoverabilityAmount, $val->transcurrency->DecimalPlaces);
                }

            }

            \Excel::create('vat_detail_report', function ($excel) use ($data) {
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
                            ->with(['supplier'=>
                                function($query){
                                    $query->with(['country']);
                                },'customer'=>
                                function($query){
                                    $query->with(['country']);
                                }
                                ,'rptcurrency','localcurrency','final_approved_by','document_master','main_category', 'sub_category'])
                            ->orderBy('taxLedgerID', 'desc');

        if($isForDataTable==0){
            $output = $output->get();
        }

        return $output;

    }

    private function getVatDetailReportQuery($input,$isForDataTable=0){

        $documentSystemIDs = [];
        $vatTypesIDs = [];
        $customerIds = [];
        $supplierIds = [];
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

        if(isset($input['customers'])){
            $customers = (array)$input['customers'];
            $customerIds = array_filter(collect($customers)->pluck('customerCodeSystem')->toArray());
        }

        if(isset($input['suppliers'])){
            $suppliers = (array)$input['suppliers'];
            $supplierIds = array_filter(collect($suppliers)->pluck('customerCodeSystem')->toArray());
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

        $output = TaxLedgerDetail::where('companySystemID',$companySystemID)
                           ->whereDate('documentDate','>=',$fromDate)
                           ->whereDate('documentDate','<=',$toDate)
                           ->when(count($documentSystemIDs) > 0, function ($query) use ($documentSystemIDs) {
                                $query->whereIn('documentSystemID',$documentSystemIDs);
                            })
                            ->when(count($vatTypesIDs) > 0, function ($query) use ($vatTypesIDs) {
                                $query->whereIn('vatSubCategoryID',$vatTypesIDs);
                            })
                            ->when($reportTypeID == 3, function ($query) use ($accountTypeIds) {
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
                            ->when($reportTypeID == 3, function ($query) use ($customerIds) {
                                $query->when(count($customerIds) > 0, function ($query) use ($customerIds) {
                                    $query->whereIn('partyAutoID',$customerIds);
                                });
                            })
                            ->when($reportTypeID == 4, function ($query) use ($supplierIds) {
                                $query->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                                    $query->whereIn('partyAutoID',$supplierIds);
                                });
                            })
                            ->when($reportTypeID == 4, function ($query) use ($accountTypeIds) {
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
                            ->when($reportTypeID == 5, function ($query) use ($supplierIds) {
                                $query->when(count($supplierIds) > 0, function ($query) use ($supplierIds) {
                                    $query->whereIn('partyAutoID',$supplierIds);
                                })
                                ->whereHas('item_detail', function($query) {
                                    $query->where('financeCategoryMaster', 3);
                                });
                            })
                            ->when($reportTypeID == 5, function ($query) use ($accountTypeIds) {
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
                            ->with(['supplier','customer','rptcurrency','localcurrency','document_master','main_category', 'sub_category', 'transcurrency', 'country', 'company' => function($query) {
                                $query->with(['country']);
                            }, 'input_vat', 'input_vat_transfer', 'output_vat', 'output_vat_transfer', 'supplier_invoice']);
        return $output;

    }
}
