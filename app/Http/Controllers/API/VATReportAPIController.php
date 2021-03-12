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
use App\Models\TaxVatMainCategories;
use App\Models\Year;
use App\Repositories\YearRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
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

        $vatTypes = TaxVatMainCategories::whereHas('tax',function ($query) use($selectedCompanyId){
            $query->where('companySystemID',$selectedCompanyId);
        })->where('isActive',1)->get();

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
            ->addColumn('document', function ($row) {
                return $this->getAmountDetailsFromDocuments($row);
            })
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
                $output= [
                    'localAmount'=> isset($row->sales_return->companyLocalAmount)?number_format($row->sales_return->companyLocalAmount,$localDecimal):number_format(0,$localDecimal),
                    'rptAmount'=> isset($row->sales_return->companyReportingAmount)?number_format($row->sales_return->companyReportingAmount,$rptDecimal):number_format(0,$rptDecimal),
                ];
                break;
            default:
                $output = [];
        }
        return $output;
    }

    public function exportVATReport(Request $request){
        $input = $request->all();
        $output = $this->getVatReportQuery($input);

        if (count((array)$output)>0) {
            $x = 0;
            $data = [];
            foreach ($output as $val) {
                $x++;

                $data[$x]['Document Type'] = $val->documentID;
                $data[$x]['Document Code'] = $val->documentCode;
                $data[$x]['Document Date'] = Helper::dateFormat($val->documentDate);
                if(in_array($val->documentSystemID, [3, 24, 11, 15,4])){
                    $data[$x]['Party Name'] =isset($val->supplier->supplierName)?$val->supplier->supplierName:'';
                }elseif (in_array($val->documentSystemID, [19, 20, 21, 71])){
                    $data[$x]['Party Name'] =isset($val->customer->CustomerName)?$val->customer->CustomerName:'';
                }else{
                    $data[$x]['Party Name'] ='';
                }

                $data[$x]['Approved By'] = isset($val->final_approved_by->empName)?$val->final_approved_by->empName:'';

                $amountArray=$this->getAmountDetailsFromDocuments($val);
                $data[$x]['Document Total Amount'] = round($amountArray['localAmount'],3);
                $data[$x]['Document VAT Amount'] = round($val->documentLocalAmount,3);
                if(isset($input['currencyID'])&&$input['currencyID']==2){
                    $data[$x]['Document Total Amount'] = round($amountArray['rptAmount'],2);
                    $data[$x]['Document VAT Amount'] = round($val->documentRptAmount,2);
                }

                $data[$x]['VAT Type'] = '';
                $data[$x]['Is Claimed'] = 'Claimed';
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
            $vatTypesIDs = array_filter(collect($vatTypes)->pluck('taxVatMainCategoriesAutoID')->toArray());
        }
        if(isset($input['fromDate'])){
            $fromDate = new Carbon($input['fromDate']);
        }

        if(isset($input['toDate'])){
            $toDate = new Carbon($input['toDate']);
        }

        //  get gl account ID
        $tax = Tax::where('taxCategory',2)->where('isActive',1)->first();

        if (empty($tax)){
            return $this->sendError('VAT not found on Tax setup');
        }

        if($reportTypeID == 1){
            $chartOfAccountID = $tax->outputVatGLAccountAutoID;
        }else{
            $chartOfAccountID = $tax->inputVatGLAccountAutoID;
        }

        $output = GeneralLedger::where('companySystemID',$companySystemID)
            ->whereDate('documentDate','>=',$fromDate)
            ->whereDate('documentDate','<=',$toDate)
            ->where('chartOfAccountSystemID',$chartOfAccountID)
            ->when(count($documentSystemIDs)>0, function ($query) use($documentSystemIDs){
                $query->whereIn('documentSystemID',$documentSystemIDs);
            })
            //            ->when(count($vatTypesIDs)>0, function ($query) use($vatTypesIDs){})
            ->with(['supplier','customer','rptcurrency','localcurrency','final_approved_by',
                'grv','material_issue','stock_return','stock_transfer',
                'receive_stock','stock_adjustment','inventory_reclassification','purchase_return',
                'customer_invoice','supplier_invoice','debit_note','credit_note',
                'payment_voucher','bank_receipt','journal_entries','fixed_asset',
                'fixed_asset_dep','fixed_asset_disposal','delivery_order','sales_return',
            ])
            ->orderBy('GeneralLedgerID', 'desc');

        if($isForDataTable==0){
            $output = $output->get();
        }

        return $output;

    }


}
