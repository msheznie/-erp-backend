<?php

namespace App\Http\Controllers\API;

use App\Exports\GeneralLedger\VAT\InputOutputVatReport;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\TaxLedger;
use App\Models\TaxLedgerDetail;
use App\Models\TaxVatCategories;
use App\Services\Excel\ExportReportToExcelService;
use App\Services\Excel\ExportVatDetailReportService;
use App\Services\GeneralLedger\Reports\VatDetailReportService;
use App\Services\GeneralLedger\Reports\VatReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $rptAmountTotal = 0;
        $documentReportingAmountTotal = 0;
        $localAmountTotal = 0;
        $documentLocalAmountTotal = 0;
        $rptDecimalPlace = 2;
        $localDecimalPlace = 2;
        if ($output) {
            foreach ($output as $val) {
                $rptAmountTotal += $val->rptAmount;
                $documentReportingAmountTotal += $val->documentReportingAmount;
                $localAmountTotal += $val->localAmount;
                $documentLocalAmountTotal += $val->documentLocalAmount;

                $rptDecimalPlace = $val->rptcurrency->DecimalPlaces;
                $localDecimalPlace = $val->localcurrency->DecimalPlaces;
            }
        }
        


        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('rptAmountTotal', $rptAmountTotal)
            ->with('documentReportingAmountTotal', $documentReportingAmountTotal)
            ->with('localAmountTotal', $localAmountTotal)
            ->with('documentLocalAmountTotal', $documentLocalAmountTotal)
            ->with('rptDecimalPlace', $rptDecimalPlace)
            ->with('localDecimalPlace', $localDecimalPlace)
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

        $taxableAmountTotal = 0;
        $VATAmountTotal = 0;
        $taxableAmountLocalTotal = 0;
        $VATAmountLocalTotal = 0;
        $recoverabilityAmountTotal = 0;
        $transdecimalPlace = '';

        if ($output) {
            foreach ($output->get() as $val) {
                $taxableAmountTotal += $val->taxableAmount;
                $VATAmountTotal += $val->VATAmount;
                $taxableAmountLocalTotal += $val->taxableAmountLocal;
                $VATAmountLocalTotal += $val->VATAmountLocal;
                $recoverabilityAmountTotal += $val->recoverabilityAmount;

                $transdecimalPlace = isset($val->transcurrency->DecimalPlaces)? $val->transcurrency->DecimalPlaces : 3;
            }
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
                        ->with('taxableAmountTotal', $taxableAmountTotal)
                        ->with('VATAmountTotal', $VATAmountTotal)
                        ->with('taxableAmountLocalTotal', $taxableAmountLocalTotal)
                        ->with('VATAmountLocalTotal', $VATAmountLocalTotal)
                        ->with('recoverabilityAmountTotal', $recoverabilityAmountTotal)
                        ->with('transdecimalPlace', $transdecimalPlace)
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

    public function exportVATReport(Request $request, ExportReportToExcelService $service, VatReportService $vatReportService){
        $input = $request->all();
        $company = Company::find($request->companySystemID);
        $output = $this->getVatReportQuery($input);
        if($request->reportTypeID == 1){
            $title = 'Output VAT Summary';
            $fileName = 'output_vat_summary';
        } elseif($request->reportTypeID == 2){
            $title = 'Input VAT Summary';
            $fileName = 'input_vat_summary';
        } else{
            $title = 'VAT Summary Report';
            $fileName = 'vat_summary_report';
        }
        $path = 'general-ledger/report/vat_report/excel/';
        if (count((array)$output)>0) {
            $data = $vatReportService->getExcelExportData($output);
            $inputOutputVatReport = new InputOutputVatReport();
            $company_name = $company->CompanyName;
            $company_code = isset($company->CompanyID)?$company->CompanyID: null;
            $to_date = $request->toDate;
            $from_date = $request->fromDate;
            $exportToExcel = $service
                ->setTitle($title)
                ->setFileName($fileName)
                ->setReportType(4)
                ->setPath($path)
                ->setCompanyCode($company_code)
                ->setCompanyName($company_name)
                ->setFromDate($from_date)
                ->setToDate($to_date)
                ->setCurrency($input['currencyID'])
                ->setData($data)
                ->setDateType(2)
                ->setType($input['type'])
                ->setExcelFormat($inputOutputVatReport->getCloumnFormat())
                ->setDetails()
                ->generateExcel();

            if(!$exportToExcel['success'])
            {
                 return $this->sendError('Unable to export excel');
            }
            else
            {
                 return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
            }
        }
        return $this->sendError( 'No Records Found');
    }




public function exportVATDetailReport(Request $request, ExportVatDetailReportService $service)
{
        $input = $request->all();
        $output = $this->getVatDetailReportQuery($input)->get();
        $vatDetailReportService = new VatDetailReportService();
        if (count((array)$output)>0) {
            $data = $vatDetailReportService->generateDataForVatDetailReport($output,$input);
            $company = Company::find($request->companySystemID);
            $company_code = isset($company->CompanyID)?$company->CompanyID: null;

            if(!empty($company)){
                $company_name = $company->CompanyName;
                $company_vat_registration_number = $company->vatRegistratonNumber;
            } else {
                $company_name = '';
                $company_vat_registration_number = '';
            }
            $to_date = $request->toDate;
            $from_date = $request->fromDate;
            

            if($request->reportTypeID == 3){
                $title = 'Details Of Outward Supply';
                $fileName = 'details_of_outward_supply';
            } elseif($request->reportTypeID == 4){
                $title = 'Details Of Inward Supply';
                $fileName = 'details_of_inward_supply';
            } elseif($request->reportTypeID == 5){
                $title = 'Details of Capital Asset Purchase';
                $fileName = 'capital_asset_purchase_details';
            }

            $path = 'general-ledger/report/vat_report/excel/';
            $exportToExcel = $service
                ->setTitle($title)
                ->setFileName($fileName)
                ->setPath($path)
                ->setCompanyCode($company_code)
                ->setCompanyName($company_name)
                ->setFromDate($from_date)
                ->setToDate($to_date)
                ->setReportType(4)
                ->setCurrency($input['currencyID'])
                ->setCompanyVatRegistrationNumber($company_vat_registration_number)
                ->setExcelFormat($service->getExcelCloumnFormat($request->reportTypeID))
                ->setData($data)
                ->setType($input['type'])
                ->setDateType(2)
                ->setDetails()
                ->generateExcel();


            if(!$exportToExcel['success'])
            {
                return $this->sendError('Unable to export excel');
            }
            else
            {
                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
            }
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
                                },
                                'employee','rptcurrency','localcurrency','final_approved_by','document_master','main_category', 'sub_category', 'supplier_invoice', 'grv', 'purchase_return', 'bank_receipt'])
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
                            }, 'input_vat', 'input_vat_transfer', 'output_vat', 'output_vat_transfer', 'supplier_invoice', 'grv', 'purchase_return']);
        return $output;

    }
}
