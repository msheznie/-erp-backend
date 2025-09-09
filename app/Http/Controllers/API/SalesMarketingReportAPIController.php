<?php
/**
 * =============================================
 * -- File Name : AccountsReceivableReportAPIControllerroller.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 9 - April 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 * -- Date: 04-June 2018 By: Mubashir Description: Added Grvmaster approved filter from reports
 * -- Date: 06-June 2018 By: Mubashir Description: Removed Grvmaster approved filter for item analaysis report
 * -- Date: 08-june 2018 By: Mubashir Description: Added new functions named as getAcountReceivableFilterData(),
 * -- Date: 18-june 2018 By: Mubashir Description: Added new functions named as pdfExportReport(),
 * -- Date: 19-june 2018 By: Mubashir Description: Added new functions named as getCustomerStatementAccountQRY(),
 * -- Date: 19-june 2018 By: Mubashir Description: Added new functions named as getCustomerBalanceStatementQRY(),
 * -- Date: 20-june 2018 By: Mubashir Description: Added new functions named as getCustomerAgingDetailQRY(),
 * -- Date: 22-june 2018 By: Mubashir Description: Added new functions named as getCustomerAgingSummaryQRY(),
 * -- Date: 29-june 2018 By: Nazir Description: Added new functions named as getCustomerCollectionQRY(),
 * -- Date: 29-june 2018 By: Mubashir Description: Added new functions named as getCustomerLedgerTemplate1QRY(),
 * -- Date: 02-july 2018 By: Fayas Description: Added new functions named as getCustomerBalanceSummery(),getCustomerRevenueMonthlySummary(),
 * -- Date: 02-July 2018 By: Nazir Description: Added new functions named as getCustomerCollectionMonthlyQRY(),
 * -- Date: 02-july 2018 By: Mubashir Description: Added new functions named as getCustomerLedgerTemplate2QRY(),
 * -- Date: 03-july 2018 By: Mubashir Description: Added new functions named as getCustomerSalesRegisterQRY(),
 * -- Date: 03-july 2018 By: Nazir Description: Added new functions named as getCustomerCollectionCNExcelQRY(),
 * -- Date: 03-july 2018 By: Nazir Description: Added new functions named as getCustomerCollectionBRVExcelQRY()
 * -- Date: 03-july 2018 By: Fayas Description: Added new functions named as getRevenueByCustomer()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueQRY()
 * -- Date: 10-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryCollectionQRY()
 * -- Date: 11-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryOutstandingQRY()
 * -- Date: 11-july 2018 By: Nazir Description: Added new functions named as getCustomerSummaryRevenueServiceLineBaseQRY()
 * -- Date: 13-February 2019 By: Nazir Description: Added new functions named as getCustomerSummaryOutstandingUpdatedQRY()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\ErpItemLedger;
use App\Models\Contract;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CurrencyMaster;
use App\Models\DeliveryOrderDetail;
use App\Models\CustomerAssigned;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Models\CustomerMasterCategory;
use App\Models\FinanceItemCategoryMaster;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\FreeBillingMasterPerforma;
use App\Models\QuotationMaster;
use App\Models\QuotationStatus;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\helper\CreateExcel;
class SalesMarketingReportAPIController extends AppBaseController
{
    /*validate each report*/
    public function validateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'SAR':
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'customer' => 'required',
                    'warehouse' => 'required',
                    'subCategory' => 'required',
                    'mainCategory' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;

                case 'qso':
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'customers' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'SDR':
                $validator = \Validator::make($request->all(), [
                    'toDate' => 'required',
                    'fromDate' => 'required',
                    'items' => 'required',
                    'currencyID' => 'required',
                    'customers' => 'required',
                    'wareHouse' => 'required',
                ],
                    ['wareHouse.required' => 'The warehouse field is required']
                );

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;

            default:
                return $this->sendError('No report ID found');
        }

    }

    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {

            case 'qso':
                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }
                
                $search = $request->input('search.value');

                $convertedRequest = (object)$this->convertArrayToSelectedValue($request->all(), array('approved_status','invoice_status','delivery_status'));
                $checkIsGroup = Company::find($convertedRequest->companySystemID);
                $output = $this->getQSOQRY($convertedRequest, $search);

                $outputArr = array();
                $invoiceAmount = collect($output)->pluck('invoice_amount')->toArray();
                $invoiceAmount = array_sum($invoiceAmount);

                $paidAmount = collect($output)->pluck('paid_amount')->toArray();
                $paidAmount = array_sum($paidAmount);

                $document_amount = collect($output)->pluck('document_amount')->toArray();
                $document_amount = array_sum($document_amount);

                $decimalPlace = collect($output)->pluck('dp')->toArray();
                $decimalPlace = array_unique($decimalPlace);

                $request->request->remove('order');
                $data['order'] = [];
                $data['search']['value'] = '';
                $request->merge($data);
                $request->request->remove('search.value');
                
                return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    // $query->orderBy('quiz_usermaster.id', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->with('companyName', $checkIsGroup->CompanyName)
                        ->with('document_amount', $document_amount)
                        ->with('paidAmount', $paidAmount)
                        ->with('invoiceAmount', $invoiceAmount)
                        ->with('currencyDecimalPlace', !empty($decimalPlace) ? $decimalPlace[0] : 2)
                        ->make(true);
                break;
            case 'SDR':
                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }
                
                $search = $request->input('search.value');


                $company = Company::find($input['companySystemID']);
                $output = $this->getSalesDetailQry($input, $search);

                $locaCurrencyID = $company->localCurrencyID;
                $reportingCurrencyID = $company->reportingCurrency;

                if(is_array($input['currencyID']))
                {
                    $input['currencyID'] = $input['currencyID'][0];
                }

                $currencyID = (isset($input['currencyID']) && $input['currencyID'] == 1) ?  $locaCurrencyID : $reportingCurrencyID;

       
                $currency = CurrencyMaster::find($currencyID);

                $request->request->remove('order');
                $data['order'] = [];
                $data['search']['value'] = '';
                $request->merge($data);
                $request->request->remove('search.value');
                $totalValue = $output;
                return \DataTables::of($output)
                        ->order(function ($query) use ($input) {
                            if (request()->has('order')) {
                                if ($input['order'][0]['column'] == 0) {
                                    // $query->orderBy('quiz_usermaster.id', $input['order'][0]['dir']);
                                }
                            }
                        })
                        ->addIndexColumn()
                        ->with('orderCondition', $sort)
                        ->with('companyName', $company->CompanyName)
                    ->with('salesValueTotal', $this->salesDetailReportTotal($input, $totalValue, 'salesValueTotal'))
                    ->with('costTotal', $this->salesDetailReportTotal($input, $totalValue, 'costTotal'))
                    ->with('profitTotal', $this->salesDetailReportTotal($input, $totalValue, 'profitTotal'))
                        ->with('CurrencyCode', $currency->CurrencyCode)
                        ->with('DecimalPlaces', $currency->DecimalPlaces)
                        ->addColumn('average_cost', function ($row) use ($input, $currency){
                            return $this->getAverageCostUpToDate($row, $input, $currency);
                        })
                        ->addColumn('discount_amount', function ($row) use ($input, $currency){
                            return $this->getDiscountAmountOfDeliveryOrder($row, $input, $currency);
                        })
                        ->make(true);

                break;
            case 'SARD':
                $customers = $request['Customer'];
                $customers = (array)$customers;
                $customers = collect($customers)->pluck('customerCodeSystem');

                $warehouses = $request['Warehouse'];
                $warehouses = (array)$warehouses;
                $warehouses = collect($warehouses)->pluck('wareHouseSystemCode');


                $subCategories = $request['subCategory'];
                $subCategories = (array)$subCategories;
                $subCategories = collect($subCategories)->pluck('id');

                $mainCategories = $request['mainCategory'];
                $mainCategories = (array)$mainCategories;
                $mainCategories = collect($mainCategories)->pluck('id');

                $from = $request->fromDate;
                $toDate = $request->toDate;
                $fromDate = new Carbon($from);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($toDate);
                $toDate = $toDate->format('Y-m-d');
                $companySystemID = $request->companySystemID;
                $currencyID = $request->currency;

                $invoiceDetails = CustomerInvoiceItemDetails::with(['local_currency','sales_return_details'=>function($query) use ($customers,$warehouses,$subCategories,$mainCategories) {
                    $query->with(['master']);
                },'reporting_currency','item_by'=>
                        function($query) use ($customers,$warehouses,$subCategories,$mainCategories) {
                            $query->with(['financeMainCategory','financeSubCategory'])->whereHas('financeSubCategory', function ($q) use ($subCategories){
                                $q->whereIn('itemCategorySubID', $subCategories);
                            })->whereHas('financeMainCategory', function ($q) use ($mainCategories){
                                $q->whereIn('itemCategoryID', $mainCategories);
                            });
                        }
                        ,'uom_default','master'=>
                        function($query) use ($customers,$warehouses){
                            $query->with(['segment','customer','warehouse'=>
                                function($query) use ($customers,$warehouses){
                                    $query->with(['location']);
                                }
                            ])
                            ->whereHas('customer', function ($q) use ($customers){
                                $q->whereIn('customerCodeSystem', $customers);
                            })
                            ->whereHas('warehouse', function ($q) use ($warehouses){
                                $q->whereIn('wareHouseSystemCode', $warehouses);
                            });

                        }
                    ])->whereHas('master', function ($q) use($fromDate,$toDate,$companySystemID){
                        $q->where('approved', "-1");
                        $q->where('canceledYN', "0");
                        $q->where('createdDateAndTime', '>=', $fromDate);
                        $q->where('createdDateAndTime', '<=', $toDate);
                        $q->where('companySystemID',$companySystemID);
                    }
                    )->get();

                $yes = 0;
                foreach ($invoiceDetails as $item1){
                    if($item1->master != null && $item1->item_by != null){
                        $yes = 1;
                    }
                }

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $output = array(
                    'items' => $invoiceDetails,
                    'company' => $company,
                    'currency'=>$currencyID,
                    'yes' => $yes,
                );

                return $this->sendResponse($output, trans('custom.items_retrieved_successfully'));
                break;
            case 'SARDS':
                $customers = $request['Customer'];
                $customers = (array)$customers;
                $customers = collect($customers)->pluck('customerCodeSystem');

                $warehouses = $request['Warehouse'];
                $warehouses = (array)$warehouses;
                $warehouses = collect($warehouses)->pluck('wareHouseSystemCode');

                $warehouse_descriptions = $request['Warehouse'];
                $warehouse_descriptions = (array)$warehouse_descriptions;
                $warehouse_descriptions = collect($warehouse_descriptions)->pluck('wareHouseDescription');

                $subCategories = $request['subCategory'];
                $subCategories = (array)$subCategories;
                $subCategories = collect($subCategories)->pluck('id');

                $mainCategories = $request['mainCategory'];
                $mainCategories = (array)$mainCategories;
                $mainCategories = collect($mainCategories)->pluck('id');

                $companySystemID = $request->companySystemID;
                $currencyID = $request->currency;

                $from = $request->fromDate;
                $toDate = $request->toDate;
                $fromDate = new Carbon($from);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($toDate);
                $toDate = $toDate->format('Y-m-d');


                $invoiceDetails = CustomerInvoiceItemDetails::with(['local_currency','reporting_currency','sales_return_details','item_by'=>
                    function($query) use ($customers,$warehouses,$subCategories,$mainCategories) {
                        $query->with(['financeMainCategory','financeSubCategory'])->whereHas('financeSubCategory', function ($q) use ($subCategories){
                            $q->whereIn('itemCategorySubID', $subCategories);
                        })->whereHas('financeMainCategory', function ($q) use ($mainCategories){
                            $q->whereIn('itemCategoryID', $mainCategories);
                        });
                    }
                    ,'uom_default','master'=>
                        function($query) use ($customers,$warehouses){
                            $query->with(['segment','customer','warehouse'=>
                                function($query) use ($customers,$warehouses){
                                    $query->with(['location']);
                                }
                            ])
                                ->whereHas('customer', function ($q) use ($customers){
                                    $q->whereIn('customerCodeSystem', $customers);
                                })
                                ->whereHas('warehouse', function ($q) use ($warehouses){
                                    $q->whereIn('wareHouseSystemCode', $warehouses);
                                });

                        }
                ])->whereHas('master', function ($q) use($fromDate,$toDate,$companySystemID){
                    $q->where('approved', "-1");
                    $q->where('canceledYN', "0");
                    $q->where('createdDateAndTime', '>=', $fromDate);
                    $q->where('createdDateAndTime', '<=', $toDate);
                    $q->where('companySystemID',$companySystemID);
                }
                )->get();

                $yes = 0;
                foreach ($invoiceDetails as $item1){
                    if($item1->master != null && $item1->item_by != null){
                        $yes = 1;
                    }
                }



                $warehouseArray = array();
                foreach ($warehouses as $warehouse) {

                    if($currencyID == 1) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->selectRaw('*, sum(qtyIssued) as totalQty')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('wareHouseSystemCode', $warehouse)->groupBy('itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.localCurrencyID')
                            ->whereIn('customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->get();
                    }
                    if($currencyID == 2) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->selectRaw('*, sum(qtyIssued) as totalQty')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('wareHouseSystemCode', $warehouse)->groupBy('itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.reportingCurrencyID')
                            ->whereIn('customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->get();
                    }


                    array_push($warehouseArray,$totalwarehouse);

                }

                $warehouseArrayItems = array();

                foreach ($warehouseArray as $item1) {
                    foreach ($item1 as $item2) {
                        array_push($warehouseArrayItems, $item2->itemPrimaryCode);
                    }
                }

                $warehouseReturnSum = array();
                foreach ($warehouses as $warehouse) {
                    $itemsSum = array();
                    foreach ($warehouseArrayItems as $item) {
                        $totalReturn = DB::table('salesreturndetails')->join('salesreturn', 'salesreturn.id', '=', 'salesreturndetails.salesReturnID')->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.customerItemDetailID', '=', 'salesreturndetails.customerItemDetailID')
                            ->where('salesreturn.wareHouseSystemCode', $warehouse)->where('salesreturn.companySystemID', $companySystemID)
                            ->groupBy('salesreturndetails.itemPrimaryCode')
                            ->where('salesreturndetails.itemPrimaryCode', $item)
                            ->where('salesreturn.approvedYN', "-1")
                            ->selectRaw('salesreturndetails.itemPrimaryCode,sum(salesreturndetails.qtyReturned) as totalReturned')
                            ->get();
                        array_push($itemsSum,$totalReturn);
                    }
                    array_push($warehouseReturnSum,$itemsSum);
                }



                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $output = array(
                    'items' => $invoiceDetails,
                    'company' => $company,
                    'warehouses' => $warehouse_descriptions,
                    'warehouseCodes' => $warehouses,
                    'totalwarehouseArray'=>$warehouseArray,
                    'currency'=>$currencyID,
                    'totalReturn'=>$warehouseReturnSum,
                    'yes'=>$yes
                );


                return $this->sendResponse($output, trans('custom.items_retrieved_successfully'));
                break;

            case 'SARDVS':
                $customers = $request['Customer'];
                $customers = (array)$customers;
                $customers = collect($customers)->pluck('customerCodeSystem');

                $warehouses = $request['Warehouse'];
                $warehouses = (array)$warehouses;
                $warehouses = collect($warehouses)->pluck('wareHouseSystemCode');

                $warehouse_descriptions = $request['Warehouse'];
                $warehouse_descriptions = (array)$warehouse_descriptions;
                $warehouse_descriptions = collect($warehouse_descriptions)->pluck('wareHouseDescription');

                $subCategories = $request['subCategory'];
                $subCategories = (array)$subCategories;
                $subCategories = collect($subCategories)->pluck('id');

                $mainCategories = $request['mainCategory'];
                $mainCategories = (array)$mainCategories;
                $mainCategories = collect($mainCategories)->pluck('id');

                $companySystemID = $request->companySystemID;
                $currencyID = $request->currency;

                $from = $request->fromDate;
                $toDate = $request->toDate;
                $fromDate = new Carbon($from);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($toDate);
                $toDate = $toDate->format('Y-m-d');

                $invoiceDetails = CustomerInvoiceItemDetails::with(['local_currency','reporting_currency','item_by'=>
                    function($query) use ($customers,$warehouses,$subCategories,$mainCategories) {
                        $query->with(['financeMainCategory','financeSubCategory'])->whereHas('financeSubCategory', function ($q) use ($subCategories){
                            $q->whereIn('itemCategorySubID', $subCategories);
                        })->whereHas('financeMainCategory', function ($q) use ($mainCategories){
                            $q->whereIn('itemCategoryID', $mainCategories);
                        });
                    }
                    ,'uom_default','master'=>
                        function($query) use ($customers,$warehouses){
                            $query->with(['segment','customer','warehouse'=>
                                function($query) use ($customers,$warehouses){
                                    $query->with(['location']);
                                }
                            ])
                                ->whereHas('customer', function ($q) use ($customers){
                                    $q->whereIn('customerCodeSystem', $customers);
                                })
                                ->whereHas('warehouse', function ($q) use ($warehouses){
                                    $q->whereIn('wareHouseSystemCode', $warehouses);
                                });

                        }
                ])->whereHas('master', function ($q) use($fromDate,$toDate,$companySystemID){
                    $q->where('approved', "-1");
                    $q->where('canceledYN', "0");
                    $q->where('createdDateAndTime', '>=', $fromDate);
                    $q->where('createdDateAndTime', '<=', $toDate);
                    $q->where('companySystemID',$companySystemID);
                }
                )->get();

                $yes = 0;
                foreach ($invoiceDetails as $item1){
                    if($item1->master != null && $item1->item_by != null){
                        $yes = 1;
                    }
                }

                $warehouseArray = array();
                foreach ($warehouses as $warehouse) {
                    if($currencyID == 1) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('erp_custinvoicedirect.wareHouseSystemCode', $warehouse)->groupBy('erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.localCurrencyID')
                            ->selectRaw('*, sum(erp_customerinvoiceitemdetails.qtyIssued) as totalQty')
                            ->whereIn('erp_custinvoicedirect.customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->orderBy('erp_customerinvoiceitemdetails.itemPrimaryCode', 'ASC')
                            ->get();

                    }
                    if($currencyID == 2) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('erp_custinvoicedirect.wareHouseSystemCode', $warehouse)->groupBy('erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.reportingCurrencyID')
                            ->selectRaw('*, sum(erp_customerinvoiceitemdetails.qtyIssued) as totalQty')
                            ->whereIn('erp_custinvoicedirect.customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->orderBy('erp_customerinvoiceitemdetails.itemPrimaryCode', 'ASC')
                            ->get();
                    }
                    array_push($warehouseArray,$totalwarehouse);

                }


                $warehouseArrayItems = array();

                foreach ($warehouseArray as $item1) {
                    foreach ($item1 as $item2) {
                        array_push($warehouseArrayItems, $item2->itemPrimaryCode);
                    }
                }

                $warehouseReturnSum = array();
                foreach ($warehouses as $warehouse) {
                    $itemsSum = array();
                    foreach ($warehouseArrayItems as $item) {
                        $totalReturn = DB::table('salesreturndetails')->join('salesreturn', 'salesreturn.id', '=', 'salesreturndetails.salesReturnID')->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.customerItemDetailID', '=', 'salesreturndetails.customerItemDetailID')
                            ->where('salesreturn.wareHouseSystemCode', $warehouse)->where('salesreturn.companySystemID', $companySystemID)
                            ->where('salesreturndetails.itemPrimaryCode', $item)
                            ->where('salesreturn.approvedYN', "-1")
                            ->selectRaw('salesreturndetails.itemPrimaryCode,sum(salesreturndetails.qtyReturned) as totalReturned')
                            ->orderBy('salesreturndetails.itemPrimaryCode', 'ASC')
                            ->groupBy('salesreturndetails.itemPrimaryCode')
                            ->get();
                        array_push($itemsSum,$totalReturn);
                    }
                    array_push($warehouseReturnSum,$itemsSum);
                }

                $warehouseArraySum = array();
                foreach ($warehouses as $warehouse) {
                    $itemArraySum = array();
                    foreach ($warehouseArrayItems as $item) {
                        $totalSumOpening = DB::table('erp_itemledger')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.wareHouseSystemCode', $warehouse)->where('erp_itemledger.companySystemID', $companySystemID)
                            ->groupBy('erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.itemPrimaryCode', $item)
                            ->where('erp_itemledger.transactionDate', '<', $fromDate)
                            ->where('itemmaster.financeCategoryMaster', 1)
                            ->selectRaw('erp_itemledger.itemPrimaryCode,sum(erp_itemledger.inOutQty) as totalOpening')
                            ->orderBy('erp_itemledger.itemPrimaryCode', 'ASC')
                            ->get();

                        $totalSumCurrent = DB::table('erp_itemledger')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.wareHouseSystemCode', $warehouse)->where('erp_itemledger.companySystemID', $companySystemID)
                            ->groupBy('erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.itemPrimaryCode', $item)
                            ->where('erp_itemledger.transactionDate', '<=', $toDate)
                            ->where('itemmaster.financeCategoryMaster', 1)
                            ->selectRaw('erp_itemledger.itemPrimaryCode,sum(erp_itemledger.inOutQty) as totalCurrent')
                            ->orderBy('erp_itemledger.itemPrimaryCode', 'ASC')
                            ->get();
                        $totalQty = array([$totalSumOpening, $totalSumCurrent]);
                        array_push($itemArraySum, $totalQty);
                    }
                    array_push($warehouseArraySum, $itemArraySum);
                }

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $output = array(
                    'items' => $invoiceDetails,
                    'company' => $company,
                    'warehouses' => $warehouse_descriptions,
                    'warehouseCodes' => $warehouses,
                    'totalwarehouseArray'=>$warehouseArray,
                    'warehouseArraySum'=>$warehouseArraySum,
                    'currency'=>$currencyID,
                    'totalReturn'=>$warehouseReturnSum,
                    'yes'=>$yes
                );

                return $this->sendResponse($output, trans('custom.items_retrieved_successfully'));
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function salesDetailReportTotal($input, $totalValue, $type)
    {
        $total = 0;
        $totalSales = (isset($input['currencyID']) && $input['currencyID'] == 1) ? collect($totalValue)->sum('localAmount') : collect($totalValue)->sum('rptAmount');
        $totalCost = (isset($input['currencyID']) && $input['currencyID'] == 1) ? collect($totalValue)->sum('localCost') : collect($totalValue)->sum('rptCost');
        switch ($type) {
            case 'salesValueTotal':
                $total = $totalSales;
                break;
            case 'costTotal':
                $total = $totalCost;
                break;
            case 'profitTotal':
                $total = $totalSales - $totalCost;
                break;
            
            default:
                # code...
                break;
        }

        return $total;
    }

    public function getAverageCostUpToDate($row, $input, $currency)
    {
        $wareHouse = isset($input['wareHouse']) ? $input['wareHouse'] : [];
        $wareHouseIds = collect($wareHouse)->pluck('id');

        $companyID = "";
        $checkIsGroup = Company::find($input['companySystemID']);
        if ($checkIsGroup->isGroup) {
            $companyID = Helper::getGroupCompany($input['companySystemID']);
        } else {
            $companyID = (array)$input['companySystemID'];
        }

         $toDate = new Carbon($input['toDate']);
        $toDate = $toDate->format('Y-m-d');

        $itemLedgerData = ErpItemLedger::selectRaw('SUM(inOutQty) as totalQty, SUM(inOutQty*wacLocal) as localTotal, SUM(inOutQty*wacRpt) as rptTotal')
                                       ->where('itemSystemCode', $row->itemCodeSystem)
                                       ->when(sizeof($wareHouseIds) > 0, function($query) use ($wareHouseIds) {
                                            $query->whereIn('wareHouseSystemCode', $wareHouseIds);
                                       })
                                       ->whereIn('companySystemID', $companyID)
                                       ->whereDate('transactionDate', '<=', $toDate)
                                       ->groupBy('itemSystemCode')
                                       ->first();

        $average_cost = 0;
        if ($itemLedgerData) {
            if ((isset($input['currencyID']) && $input['currencyID'] == 1 && $itemLedgerData->localTotal > 0) || (isset($input['currencyID']) && $input['currencyID'] == 2 && $itemLedgerData->rptTotal > 0)) {
                if($itemLedgerData->totalQty != 0) {
                    $average_cost = (isset($input['currencyID']) && $input['currencyID'] == 1) ? ($itemLedgerData->localTotal / $itemLedgerData->totalQty) : ($itemLedgerData->rptTotal / $itemLedgerData->totalQty);
                }
            }
        } 

        return round($average_cost, $currency->DecimalPlaces);
    }

    public function getDiscountAmountOfDeliveryOrder($row, $input, $currency)
    {
        $discount_amount = 0;

        if ($row->documentSystemID == 71) {
            $currencyConversionDiscount = \Helper::currencyConversion($row->companySystemID, $currency->currencyID, $currency->currencyID, $row->discountAmount);

            if (isset($input['currencyID']) && $input['currencyID'] == 1)
            {
                $discount_amount = $currencyConversionDiscount['localAmount'] * $row->quantity;
            } else {
                $discount_amount = $currencyConversionDiscount['reportingAmount'] * $row->quantity;
            }
        } 

        return round($discount_amount, $currency->DecimalPlaces);
    }

    public function getSalesDetailQry($input, $search)
    {
        $fromDate = new Carbon($input['fromDate']);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($input['toDate']);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($input['companySystemID']);
        if ($checkIsGroup->isGroup) {
            $companyID = Helper::getGroupCompany($input['companySystemID']);
        } else {
            $companyID = (array)$input['companySystemID'];
        }

        $customers = isset($input['customers']) ? $input['customers'] : [];
        $customerIds = collect($customers)->pluck('customerCodeSystem');

        $items = isset($input['items']) ? $input['items'] : [];
        $itemIds = collect($items)->pluck('itemCodeSystem');

        $wareHouse = isset($input['wareHouse']) ? $input['wareHouse'] : [];
        $wareHouseIds = collect($wareHouse)->pluck('id');


        $customer_category = isset($input['customer_category']) ? $input['customer_category'] : [];
        $customer_category_ids = collect($customer_category)->pluck('id');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
        }

        $companyIDArray = implode(',', $companyID);

        $customerIds = (array) $customerIds;
        $stringCustomer = '';

        foreach($customerIds as $a)
        {
            foreach($a as $b=>$c)
            {
                $stringCustomer .= $c.',';
            }
        }

        $customerIDArray = substr($stringCustomer,0,-1);


        $itemIds = (array) $itemIds;
        $stringItem = '';

        foreach($itemIds as $a)
        {
            foreach($a as $b=>$c)
            {
                $stringItem .= $c.',';
            }
        }

        $itemIDArray = substr($stringItem,0,-1);

        $wareHouseIds = (array) $wareHouseIds;
        $stringWareHouse = '';

        foreach($wareHouseIds as $a)
        {
            foreach($a as $b=>$c)
            {
                $stringWareHouse .= $c.',';
            }
        }

        $wareHouseIDArray = substr($stringWareHouse,0,-1);

        $toDate = json_encode(['toDateSql' => $toDate]);
        $fromDate = json_encode(['fromDateSql' => $fromDate]);
        $companyIDArray = json_encode(['companyIDSql' => $companyIDArray]);


        $customerIDArray = json_encode(['customerIDSql' => $customerIDArray]);
        $itemIDArray = json_encode(['itemIDSql' => $itemIDArray]);
        $wareHouseIDArray = json_encode(['warehouseIDSql' => $wareHouseIDArray]);
        $searchArray = json_encode(['searchSql' => $search]);

        $salesInvoiceDetail = DB::select('CALL getSalesDetailQry(:toDate,:fromDate,:company,:customer,:item,:warehouse, :search)', ['toDate' => $toDate,'fromDate' => $fromDate,'company' => $companyIDArray , 'customer' => $customerIDArray, 'item' => $itemIDArray, 'warehouse' => $wareHouseIDArray, 'search' => $searchArray]);

        if ($search) {

            $searchArray = json_encode(['searchSql' => $search]);
          
            $fromDate = new Carbon($input['fromDate']);
            $fromDate = $fromDate->format('Y-m-d');

            $toDate = new Carbon($input['toDate']);
            $toDate = $toDate->format('Y-m-d');
            $toDate = json_encode(['toDateSql' => $toDate]);
            $fromDate = json_encode(['fromDateSql' => $fromDate]);

            $salesInvoiceDetail = DB::select('CALL getSalesDetailQry(:toDate,:fromDate,:company,:customer,:item,:warehouse,:search)', ['toDate' => $toDate,'fromDate' => $fromDate,'company' => $companyIDArray , 'customer' => $customerIDArray, 'item' => $itemIDArray, 'warehouse' => $wareHouseIDArray, 'search' => $searchArray]);

        }

        return $salesInvoiceDetail;

    }

    public function generateSoldQty(Request $request){
//        $itemPrimaryCode = $request->itemPrimaryCode;
//        $totQtyIssued= CustomerInvoiceItemDetails::where('itemPrimaryCode', $itemPrimaryCode)->sum('qtyIssued');
        return $this->sendResponse("10", 'Qty Sent');

    }

    public function exportReport(Request $request)
    {

        $input = $request->all();
        $reportID = $request->reportID;
        $type = $request->type;
        switch ($reportID) {

            case 'qso':

                $convertedRequest = (object)$this->convertArrayToSelectedValue($request->all(), array('approved_status','invoice_status','delivery_status'));
                $checkIsGroup = Company::find($convertedRequest->companySystemID);
                $output = $this->getQSOQRY($convertedRequest);

                $outputArr = array();
                $invoiceAmount = collect($output)->pluck('invoice_amount')->toArray();
                $invoiceAmount = array_sum($invoiceAmount);

                $paidAmount = collect($output)->pluck('paid_amount')->toArray();
                $paidAmount = array_sum($paidAmount);

                $document_amount = collect($output)->pluck('document_amount')->toArray();
                $document_amount = array_sum($document_amount);

                $decimalPlace = collect($output)->pluck('dp')->toArray();
                $decimalPlace = array_unique($decimalPlace);
                $data = array();
                if ($output) {
                    foreach ($output as $val) {
                        // doc status
                        $doc_status = '';
                         if ($val['confirmedYN'] == 0 && $val['approvedYN'] == 0) {
                             $doc_status = "Not Confirmed";
                        }
                        else if ($val['confirmedYN'] == 1 && $val['approvedYN'] == 0 && $val['refferedBackYN'] == 0) {
                            $doc_status = "Pending Approval";
                        } else if ($val['confirmedYN'] == 1 && $val['approvedYN'] == 0 && $val['refferedBackYN'] == -1) {
                            $doc_status = "Referred Back";
                        }
                        else if ($val['confirmedYN'] == 1 && ($val['approvedYN'] == -1 || $val['refferedBackYN'] == 1 )) {
                            $doc_status = "Fully Approved";
                        }

                        //deliveryStatus
                        $delivery_status = '';
                        $return_status = '';
                        if ($val['deliveryStatus'] == 0) {
                            $delivery_status = 'Not Delivered';
                        } else if ($val['deliveryStatus'] == 1) {
                            $delivery_status = 'Partially Delivered';
                        } else if ($val['deliveryStatus'] == 2) {
                            $delivery_status = 'Fully Delivered';
                        }

                        if ($val['is_return'] == true) {
                            $return_status = ', Order Returned';
                        }


                        $dp = (isset($val['dp']) && $val['dp'])?$val['dp']:3;


                        $data[] = array(
                            'Document Code' => $val['quotationCode'],
                            'Document Date' => Helper::dateFormat($val['documentDate']),
                            'Segment' => $val['serviceLine'],
                            'Ref No' => $val['referenceNo'],
                            'Customer' => $val['customer'],
                            'Currency' => $val['currency'],
                            'Expire Date' => Helper::dateFormat($val['documentExpDate']),
                            'Document Status' => $doc_status,
                            'Customer Status' => $val['customer_status'],
                            'Document Amount' => round($val['document_amount'], $dp),
                            'Delivery/Return Status' => $delivery_status.''.$return_status,
                            'Invoice Amount' => round($val['invoice_amount'], $dp),
                            'Paid Amount' => round($val['paid_amount'], $dp)
                        );
                    }


                }
                $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
                $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                );

                $fileName = 'quotation_so_report';
                $path = 'sales/report/quotation_so_report/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;

            case 'SARD':
                $customers = $request['Customer'];
                $customers = (array)$customers;
                $customers = collect($customers)->pluck('customerCodeSystem');

                $warehouses = $request['Warehouse'];
                $warehouses = (array)$warehouses;
                $warehouses = collect($warehouses)->pluck('wareHouseSystemCode');

                $subCategories = $request['subCategory'];
                $subCategories = (array)$subCategories;
                $subCategories = collect($subCategories)->pluck('id');

                $mainCategories = $request['mainCategory'];
                $mainCategories = (array)$mainCategories;
                $mainCategories = collect($mainCategories)->pluck('id');

                $companySystemID = $request->companySystemID;
                $currencyID = $request->currency;

                $from = $request->fromDate;
                $toDate = $request->toDate;
                $fromDate = new Carbon($from);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($toDate);
                $toDate = $toDate->format('Y-m-d');

                $invoiceDetails = CustomerInvoiceItemDetails::with(['local_currency','reporting_currency','sales_return_details'=>function($query) use ($customers,$warehouses,$subCategories,$mainCategories) {
                    $query->with(['master']);
                },'item_by'=>
                    function($query) use ($customers,$warehouses,$subCategories,$mainCategories) {
                        $query->with(['financeMainCategory','financeSubCategory'])->whereHas('financeSubCategory', function ($q) use ($subCategories){
                            $q->whereIn('itemCategorySubID', $subCategories);
                        })->whereHas('financeMainCategory', function ($q) use ($mainCategories){
                            $q->whereIn('itemCategoryID', $mainCategories);
                        });
                    }
                        ,'uom_default','master'=>
                        function($query) use ($customers,$warehouses){
                            $query->with(['segment','customer','warehouse'=>
                                function($query) use ($customers,$warehouses){
                                    $query->with(['location']);
                                }
                            ])
                                ->whereHas('customer', function ($q) use ($customers){
                                    $q->whereIn('customerCodeSystem', $customers);
                                })
                                ->whereHas('warehouse', function ($q) use ($warehouses){
                                    $q->whereIn('wareHouseSystemCode', $warehouses);
                                });
                        }
                    ])->whereHas('master', function ($q) use($fromDate,$toDate,$companySystemID){
                        $q->where('approved', "-1");
                        $q->where('canceledYN', "0");
                        $q->where('createdDateAndTime', '>=', $fromDate);
                        $q->where('createdDateAndTime', '<=', $toDate);
                        $q->where('companySystemID',$companySystemID);
                    }
                    )->get();

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';
                $templateName = "export_report.sales_analysis_detail_report";

                $reportData = ['invoiceDetails' => $invoiceDetails, 'company' => $company, 'fromDate' => $fromDate, 'toDate' => $toDate, 'currencyID' => $currencyID, 'companyCode'=>$companyCode];

                $fileName = 'sales_analysis_detail_report';

                $path = 'procurement/report/sales_analysis_detail_report/excel/';

                $file_type = $request->type;

                $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);

                if($basePath == '')
                {
                    return $this->sendError('Unable to export excel');
                }
                else
                {
                    return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            case 'SARDS':
                $customers = $request['Customer'];
                $customers = (array)$customers;
                $customers = collect($customers)->pluck('customerCodeSystem');

                $warehouses = $request['Warehouse'];
                $warehouses = (array)$warehouses;
                $warehouses = collect($warehouses)->pluck('wareHouseSystemCode');

                $warehouse_descriptions = $request['Warehouse'];
                $warehouse_descriptions = (array)$warehouse_descriptions;
                $warehouse_descriptions = collect($warehouse_descriptions)->pluck('wareHouseDescription');

                $subCategories = $request['subCategory'];
                $subCategories = (array)$subCategories;
                $subCategories = collect($subCategories)->pluck('id');

                $mainCategories = $request['mainCategory'];
                $mainCategories = (array)$mainCategories;
                $mainCategories = collect($mainCategories)->pluck('id');

                $companySystemID = $request->companySystemID;
                $currencyID = $request->currency;

                $from = $request->fromDate;
                $toDate = $request->toDate;
                $fromDate = new Carbon($from);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($toDate);
                $toDate = $toDate->format('Y-m-d');


                $warehouseArray = array();
                foreach ($warehouses as $warehouse) {

                    if($currencyID == 1) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->selectRaw('*, sum(qtyIssued) as totalQty')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('wareHouseSystemCode', $warehouse)->groupBy('itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.localCurrencyID')
                            ->whereIn('customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->get();
                    }
                    if($currencyID == 2) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->selectRaw('*, sum(qtyIssued) as totalQty')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('wareHouseSystemCode', $warehouse)->groupBy('itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.reportingCurrencyID')
                            ->whereIn('customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->get();
                    }


                    array_push($warehouseArray,$totalwarehouse);

                }

                $warehouseArrayItems = array();

                foreach ($warehouseArray as $item1) {
                    foreach ($item1 as $item2) {
                        array_push($warehouseArrayItems, $item2->itemPrimaryCode);
                    }
                }

                $warehouseReturnSum = array();
                foreach ($warehouses as $warehouse) {
                    $itemsSum = array();
                    foreach ($warehouseArrayItems as $item) {
                        $totalReturn = DB::table('salesreturndetails')->join('salesreturn', 'salesreturn.id', '=', 'salesreturndetails.salesReturnID')->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.customerItemDetailID', '=', 'salesreturndetails.customerItemDetailID')
                            ->where('salesreturn.wareHouseSystemCode', $warehouse)->where('salesreturn.companySystemID', $companySystemID)
                            ->groupBy('salesreturndetails.itemPrimaryCode')
                            ->where('salesreturndetails.itemPrimaryCode', $item)
                            ->where('salesreturn.approvedYN', "-1")
                            ->selectRaw('salesreturndetails.itemPrimaryCode,sum(salesreturndetails.qtyReturned) as totalReturned')
                            ->get();
                        array_push($itemsSum,$totalReturn);
                    }
                    array_push($warehouseReturnSum,$itemsSum);
                }


                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';


                $templateName = "export_report.sales_analysis_detail_summary_report";


                $reportData = ['warehouses' => $warehouse_descriptions, 'warehouseCodes' => $warehouses,'invoiceDetails' => $warehouseArray, 'company' => $company, 'fromDate' => $fromDate, 'toDate' => $toDate,'currencyID'=>$currencyID,'totalReturn'=>$warehouseReturnSum, 'companyCode'=>$companyCode];

                $fileName = 'sales_analysis_summary_report';

                $path = 'procurement/report/sales_analysis_detail_summary_report/excel/';

                $file_type = $request->type;

                $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);

                if($basePath == '')
                {
                    return $this->sendError('Unable to export excel');
                }
                else
                {
                    return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            case 'SARDVS':
                $customers = $request['Customer'];
                $customers = (array)$customers;
                $customers = collect($customers)->pluck('customerCodeSystem');

                $warehouses = $request['Warehouse'];
                $warehouses = (array)$warehouses;
                $warehouses = collect($warehouses)->pluck('wareHouseSystemCode');

                $warehouse_descriptions = $request['Warehouse'];
                $warehouse_descriptions = (array)$warehouse_descriptions;
                $warehouse_descriptions = collect($warehouse_descriptions)->pluck('wareHouseDescription');

                $subCategories = $request['subCategory'];
                $subCategories = (array)$subCategories;
                $subCategories = collect($subCategories)->pluck('id');

                $mainCategories = $request['mainCategory'];
                $mainCategories = (array)$mainCategories;
                $mainCategories = collect($mainCategories)->pluck('id');

                $companySystemID = $request->companySystemID;
                $currencyID = $request->currency;

                $from = $request->fromDate;
                $toDate = $request->toDate;
                $fromDate = new Carbon($from);
                $fromDate = $fromDate->format('Y-m-d');

                $toDate = new Carbon($toDate);
                $toDate = $toDate->format('Y-m-d');



                $warehouseArray = array();
                foreach ($warehouses as $warehouse) {
                    if($currencyID == 1) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('erp_custinvoicedirect.wareHouseSystemCode', $warehouse)->groupBy('erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.localCurrencyID')
                            ->selectRaw('*, sum(erp_customerinvoiceitemdetails.qtyIssued) as totalQty')
                            ->whereIn('erp_custinvoicedirect.customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->orderBy('erp_customerinvoiceitemdetails.itemPrimaryCode', 'ASC')
                            ->get();
                    }
                    if($currencyID == 2) {
                        $totalwarehouse = DB::table('erp_custinvoicedirect')
                            ->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.custInvoiceDirectAutoID', '=', 'erp_custinvoicedirect.custInvoiceDirectAutoID')
                            ->where('erp_custinvoicedirect.companySystemID', $companySystemID)
                            ->where('approved', "-1")->where('canceledYN', "0")->where('createdDateAndTime', '>=', $fromDate)->where('createdDateAndTime', '<=', $toDate)
                            ->where('erp_custinvoicedirect.wareHouseSystemCode', $warehouse)->groupBy('erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'erp_custinvoicedirect.serviceLineSystemID')
                            ->join('units', 'units.UnitID', '=', 'erp_customerinvoiceitemdetails.unitOfMeasureIssued')
                            ->join('financeitemcategorysub', 'financeitemcategorysub.itemCategorySubID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategorySubID')
                            ->join('financeitemcategorymaster', 'financeitemcategorymaster.itemCategoryID', '=', 'erp_customerinvoiceitemdetails.itemFinanceCategoryID')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_customerinvoiceitemdetails.itemPrimaryCode')
                            ->join('currencymaster', 'currencymaster.currencyID', '=', 'erp_customerinvoiceitemdetails.reportingCurrencyID')
                            ->selectRaw('*, sum(erp_customerinvoiceitemdetails.qtyIssued) as totalQty')
                            ->whereIn('erp_custinvoicedirect.customerID', $customers)
                            ->whereIn('financeitemcategorysub.itemCategorySubID', $subCategories)
                            ->whereIn('financeitemcategorymaster.itemCategoryID', $mainCategories)
                            ->orderBy('erp_customerinvoiceitemdetails.itemPrimaryCode', 'ASC')
                            ->get();
                    }
                    array_push($warehouseArray,$totalwarehouse);

                }

                $warehouseArrayItems = array();

                foreach ($warehouseArray as $item1) {
                    foreach ($item1 as $item2) {
                        array_push($warehouseArrayItems, $item2->itemPrimaryCode);
                    }
                }

                $warehouseReturnSum = array();
                foreach ($warehouses as $warehouse) {
                    $itemsSum = array();
                    foreach ($warehouseArrayItems as $item) {
                        $totalReturn = DB::table('salesreturndetails')->join('salesreturn', 'salesreturn.id', '=', 'salesreturndetails.salesReturnID')->join('erp_customerinvoiceitemdetails', 'erp_customerinvoiceitemdetails.customerItemDetailID', '=', 'salesreturndetails.customerItemDetailID')
                            ->where('salesreturn.wareHouseSystemCode', $warehouse)->where('salesreturn.companySystemID', $companySystemID)
                            ->groupBy('salesreturndetails.itemPrimaryCode')
                            ->where('salesreturndetails.itemPrimaryCode', $item)
                            ->where('salesreturn.approvedYN', "-1")
                            ->selectRaw('salesreturndetails.itemPrimaryCode,sum(salesreturndetails.qtyReturned) as totalReturned')
                            ->orderBy('salesreturndetails.itemPrimaryCode', 'ASC')

                            ->get();
                        array_push($itemsSum,$totalReturn);
                    }
                    array_push($warehouseReturnSum,$itemsSum);
                }



                $warehouseArraySum = array();
                foreach ($warehouses as $warehouse) {
                    $itemArraySum = array();
                    foreach ($warehouseArrayItems as $item) {
                        $totalSumOpening = DB::table('erp_itemledger')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.wareHouseSystemCode', $warehouse)->where('erp_itemledger.companySystemID', $companySystemID)
                            ->groupBy('erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.itemPrimaryCode', $item)
                            ->where('erp_itemledger.transactionDate', '<', $fromDate)
                            ->where('itemmaster.financeCategoryMaster', 1)
                            ->selectRaw('erp_itemledger.itemPrimaryCode,sum(erp_itemledger.inOutQty) as totalOpening')
                            ->orderBy('erp_itemledger.itemPrimaryCode', 'ASC')
                            ->get();

                        $totalSumCurrent = DB::table('erp_itemledger')
                            ->join('itemmaster', 'itemmaster.primaryCode', '=', 'erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.wareHouseSystemCode', $warehouse)->where('erp_itemledger.companySystemID', $companySystemID)
                            ->groupBy('erp_itemledger.itemPrimaryCode')
                            ->where('erp_itemledger.itemPrimaryCode', $item)
                            ->where('erp_itemledger.transactionDate', '<=', $toDate)
                            ->where('itemmaster.financeCategoryMaster', 1)
                            ->selectRaw('erp_itemledger.itemPrimaryCode,sum(erp_itemledger.inOutQty) as totalCurrent')
                            ->orderBy('erp_itemledger.itemPrimaryCode', 'ASC')
                            ->get();
                        $totalQty = array([$totalSumOpening, $totalSumCurrent]);
                        array_push($itemArraySum, $totalQty);
                    }
                    array_push($warehouseArraySum, $itemArraySum);
                }

                $company = Company::with(['reportingcurrency', 'localcurrency'])->find($request->companySystemID);
                $companyCode = isset($company->CompanyID)?$company->CompanyID:'common';

                $templateName = "export_report.sales_analysis_detail_vs_soh_report";

                $reportData = ['warehouses' => $warehouse_descriptions, 'warehouseCodes' => $warehouses,'invoiceDetails' => $warehouseArray, 'warehouseArraySum' => $warehouseArraySum, 'company' => $company, 'fromDate' => $fromDate, 'toDate' => $toDate, 'currencyID'=>$currencyID,'totalReturn'=>$warehouseReturnSum, 'companyCode'=>$companyCode];

                $fileName = 'sales_analysis_soh_report';

                $path = 'procurement/report/sales_analysis_detail_vs_soh_report/excel/';

                $file_type = $request->type;

                $basePath = CreateExcel::loadView($reportData,$file_type,$fileName,$path,$templateName);

                if($basePath == '')
                {
                    return $this->sendError('Unable to export excel');
                }
                else
                {
                    return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            case 'SDR':
                $input = $request->all();
                if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
                    $sort = 'asc';
                } else {
                    $sort = 'desc';
                }
                
                $search = $request->input('search.value');


                $company = Company::find($input['companySystemID']);
                $output = $this->getSalesDetailQry($input, $search);
                $companyCode = isset($company->CompanyID) ? $company->CompanyID: null;

                $locaCurrencyID = $company->localCurrencyID;
                $reportingCurrencyID = $company->reportingCurrency;

                $currencyID = (isset($input['currencyID']) && $input['currencyID'] == 1) ?  $locaCurrencyID : $reportingCurrencyID;

                $currency = CurrencyMaster::find($currencyID);
                $data = [];
                $reportData = array('company_code' => $companyCode);
                if ($output) {
                    foreach ($output as $key => $value) {
                        $profit = (isset($input['currencyID']) && $input['currencyID'] == 1) ? floatval($value->localAmount) - floatval($value->localCost) : floatval($value->rptAmount) - floatval($value->rptCost);

                        $salesTotal = (isset($input['currencyID']) && $input['currencyID'] == 1) ? floatval($value->localAmount)  : floatval($value->rptAmount);

                        $percentage = ($value->documentSystemID == 87) ? -100 : 100;

                        if($salesTotal == 0)
                        {
                            $profitMargin = "0";
                            
                        }
                        else
                        {
                            $profitMargin = ($profit/$salesTotal) * $percentage;
                        }

                        $data[] = array(
                            'Customer Code' => $value->customerCode,
                            'Customer Name' => $value->customerName,
                            'Document System Code' => $value->documentCode,
                            'Document Date' => Helper::dateFormat($value->documentDate),
                            'Item Code' => $value->itemCode,
                            'Item Description' => $value->itemDescription,
                            'Sub Category' => $value->categoryDescription,
                            'UOM' => $value->unitShortCode,
                            'Quantity' => $value->quantity,
                            'Total Sales Value ('.$currency->CurrencyCode.')' => (isset($input['currencyID']) && $input['currencyID'] == 1) ? round(($value->localAmount - $this->getDiscountAmountOfDeliveryOrder($value, $input, $currency)), $currency->DecimalPlaces) : round(($value->rptAmount - $this->getDiscountAmountOfDeliveryOrder($value, $input, $currency)), $currency->DecimalPlaces),
                            'Total Cost ('.$currency->CurrencyCode.')' => (isset($input['currencyID']) && $input['currencyID'] == 1) ? round($value->localCost, $currency->DecimalPlaces) : round($value->rptCost, $currency->DecimalPlaces),
                            'Profit ('.$currency->CurrencyCode.')' => round($profit, $currency->DecimalPlaces),
                            'Profit Margin' => $profitMargin,
                            'Average Cost ('.$currency->CurrencyCode.') Up to Date' => $this->getAverageCostUpToDate($value, $input, $currency)
                           
                        );
                    }
                }

                $fileName = 'sales_detail_report_';
                $path = 'sales/report/sales_detail_report_/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$reportData);

                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            default:
                return $this->sendError('No report ID found');
        }

    }

    public function pdfExportReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'CS':
                if ($request->reportTypeID == 'CSA') {
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $customerName = CustomerMaster::find($request->singleCustomer);

                    $companyLogo = $checkIsGroup->logo_url;

                    $output = $this->getCustomerStatementAccountQRY($request);

                    $balanceTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $balanceTotal = array_sum($balanceTotal);

                    $receiptAmount = collect($output)->pluck('receiptAmount')->toArray();
                    $receiptAmount = array_sum($receiptAmount);

                    $invoiceAmount = collect($output)->pluck('invoiceAmount')->toArray();
                    $invoiceAmount = array_sum($invoiceAmount);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    $currencyCode = "";
                    $currency = \Helper::companyCurrency($request->companySystemID);

                    if ($request->currencyID == 2) {
                        $currencyCode = $currency->localcurrency->CurrencyCode;
                    }
                    if ($request->currencyID == 3) {
                        $currencyCode = $currency->reportingcurrency->CurrencyCode;
                    }

                    $outputArr = array();

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->documentCurrency][] = $val;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'balanceAmount' => $balanceTotal, 'receiptAmount' => $receiptAmount, 'invoiceAmount' => $invoiceAmount, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'customerName' => $customerName->customerShortCode . ' - ' . $customerName->CustomerName, 'reportDate' => date('d/m/Y H:i:s A'), 'currency' => 'Currency: ' . $currencyCode, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'currencyID' => $request->currencyID);

                    $html = view('print.customer_statement_of_account_pdf', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                } elseif ($request->reportTypeID == 'CBS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerBalanceStatementQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();
                    $grandTotal = collect($output)->pluck('balanceAmount')->toArray();
                    $grandTotal = array_sum($grandTotal);

                    $decimalPlace = collect($output)->pluck('balanceDecimalPlaces')->toArray();
                    $decimalPlace = array_unique($decimalPlace);

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'grandTotal' => $grandTotal, 'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2, 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.customer_balance_statement', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CR':
                if ($request->reportTypeID == 'RMS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerRevenueMonthlySummary($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $currency = $request->currencyID;
                    $currencyId = 2;

                    if ($currency == 2) {
                        $decimalPlaceCollect = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    } else {
                        $decimalPlaceCollect = collect($output)->pluck('documentRptCurrencyID')->toArray();
                        $decimalPlaceUnique = array_unique($decimalPlaceCollect);
                    }

                    if (!empty($decimalPlaceUnique)) {
                        $currencyId = $decimalPlaceUnique[0];
                    }


                    $requestCurrency = CurrencyMaster::where('currencyID', $currencyId)->first();

                    $decimalPlace = !empty($requestCurrency) ? $requestCurrency->DecimalPlaces : 2;

                    $total = array();

                    $total['Jan'] = array_sum(collect($output)->pluck('Jan')->toArray());
                    $total['Feb'] = array_sum(collect($output)->pluck('Feb')->toArray());
                    $total['March'] = array_sum(collect($output)->pluck('March')->toArray());
                    $total['April'] = array_sum(collect($output)->pluck('April')->toArray());
                    $total['May'] = array_sum(collect($output)->pluck('May')->toArray());
                    $total['June'] = array_sum(collect($output)->pluck('June')->toArray());
                    $total['July'] = array_sum(collect($output)->pluck('July')->toArray());
                    $total['Aug'] = array_sum(collect($output)->pluck('Aug')->toArray());
                    $total['Sept'] = array_sum(collect($output)->pluck('Sept')->toArray());
                    $total['Oct'] = array_sum(collect($output)->pluck('Oct')->toArray());
                    $total['Nov'] = array_sum(collect($output)->pluck('Nov')->toArray());
                    $total['Dece'] = array_sum(collect($output)->pluck('Dece')->toArray());
                    $total['Total'] = array_sum(collect($output)->pluck('Total')->toArray());


                    $outputArr = array();
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][] = $val;
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlace, 'total' => $total, 'currency' => $requestCurrency->CurrencyCode, 'year' => $request->year, 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.revenue_monthly_summary', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CA':
                if ($request->reportTypeID == 'CAS') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingSummaryQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->concatCompanyName][$val->documentCurrency][] = $val;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $output['aging'], 'fromDate' => \Helper::dateFormat($request->fromDate));

                    $html = view('print.customer_aging_summary', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();

                } elseif ($request->reportTypeID == 'CAD') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerAgingDetailQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();
                    $customerCreditDays = array();
                    $grandTotalArr = array();
                    if ($output['aging']) {
                        foreach ($output['aging'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    if ($output['data']) {
                        foreach ($output['data'] as $val) {
                            $outputArr[$val->customerName][$val->documentCurrency][] = $val;
                            $customerCreditDays[$val->customerName] = $val->creditDays;
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    $invoiceAmountTotal = collect($output['data'])->pluck('invoiceAmount')->toArray();
                    $invoiceAmountTotal = array_sum($invoiceAmountTotal);

                    $dataArr = array('reportData' => (object)$outputArr, 'customerCreditDays' => $customerCreditDays, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'currencyDecimalPlace' => $decimalPlaces, 'grandTotal' => $grandTotalArr, 'agingRange' => $output['aging'], 'fromDate' => \Helper::dateFormat($request->fromDate), 'invoiceAmountTotal' => $invoiceAmountTotal);

                    $html = view('print.customer_aging_detail', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            case 'CC':
                if ($request->reportTypeID == 'CCR') {

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->getCustomerCollectionQRY($request);

                    $companyLogo = $checkIsGroup->logo_url;

                    $outputArr = array();

                    $bankPaymentTotal = collect($output)->pluck('BRVDocumentAmount')->toArray();
                    $bankPaymentTotal = array_sum($bankPaymentTotal);

                    $creditNoteTotal = collect($output)->pluck('CNDocumentAmount')->toArray();
                    $creditNoteTotal = array_sum($creditNoteTotal);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->localcurrency->CurrencyCode;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                            $selectedCurrency = $companyCurrency->reportingcurrency->CurrencyCode;
                        }
                    }

                    if ($output) {
                        foreach ($output as $val) {
                            $outputArr[$val->CompanyName][$val->companyID][] = $val;
                        }
                    }

                    $dataArr = array('reportData' => (object)$outputArr, 'companyName' => $checkIsGroup->CompanyName, 'companylogo' => $companyLogo, 'decimalPlaces' => $decimalPlaces, 'fromDate' => \Helper::dateFormat($request->fromDate), 'toDate' => \Helper::dateFormat($request->toDate), 'selectedCurrency' => $selectedCurrency, 'bankPaymentTotal' => $bankPaymentTotal, 'creditNoteTotal' => $creditNoteTotal);

                    $html = view('print.customer_collection', $dataArr);

                    $pdf = \App::make('dompdf.wrapper');
                    $pdf->loadHTML($html);

                    return $pdf->setPaper('a4', 'landscape')->setWarnings(false)->stream();
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function getSalesMarketFilterData(Request $request)
    {
       
        $selectedCompanyId = $request['selectedCompanyId'];
        if($request['reportID'] == "SDR" || $request['reportID'] == "SAR")
        {

                $customerCategoryID = collect($request['customerCategoryID'])->pluck('id')->toArray();
        }
        else
        {
            $customerCategoryID = $request['customerCategoryID'];
        }
        
     
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

//        $departments = Helper::getCompanyServiceline($selectedCompanyId);
//
//        $departments[] = array("serviceLineSystemID" => 24, "ServiceLineCode" => 'X', "serviceLineMasterCode" => 'X', "ServiceLineDes" => 'X');

        $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)
            ->groupBy('customerCodeSystem')
            ->orderBy('CustomerName', 'ASC')
            ->WhereNotNull('customerCodeSystem');

            if($request['reportID'] == "SDR" || $request['reportID'] == "SAR")
        {
            if (!is_null($customerCategoryID) && count($customerCategoryID) > 0) {
                 $customerMaster = $customerMaster->whereHas('customer_master', function($query) use ($customerCategoryID) {
                                                        $query->whereIn('customerCategoryID', $customerCategoryID);
                                                });
            }
        }
        else
        {
            if (!is_null($customerCategoryID) && $customerCategoryID > 0) {
                $customerMaster = $customerMaster->whereHas('customer_master', function($query) use ($customerCategoryID) {
                                                        $query->where('customerCategoryID', $customerCategoryID);
                                                });
            }
        }



        $customerMaster = $customerMaster->get();

        $wareHouses = WarehouseMaster::whereIn("companySystemID", $companiesByGroup)->where('isActive', 1)->get();
        $financeCategoryMasters = FinanceItemCategoryMaster::all();

        $customerCategories = CustomerMasterCategory::all();
        $output = array(
            'customers' => $customerMaster,
            'customerCategories' => $customerCategories,
            'financeCategoryMasters' => $financeCategoryMasters,
            'wareHouses' => $wareHouses
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    public function getSalesAnalysisFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $customerMaster = CustomerAssigned::whereIN('companySystemID', $companiesByGroup)
            ->groupBy('customerCodeSystem')
            ->orderBy('CustomerName', 'ASC')
            ->WhereNotNull('customerCodeSystem');


        $customerMaster = $customerMaster->get();

        $warehouse = WarehouseMaster::whereIN('companySystemID', $companiesByGroup)->get();
        $document = DocumentMaster::where('departmentSystemID', 10)->get();
        $segment = SegmentMaster::ofCompany($companiesByGroup)->get();


        $item = DB::table('erp_itemledger')->select('erp_itemledger.companySystemID', 'erp_itemledger.itemSystemCode', 'erp_itemledger.itemPrimaryCode', 'erp_itemledger.itemDescription', 'itemmaster.secondaryItemCode')
            ->join('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
            ->whereIn('erp_itemledger.companySystemID', $companiesByGroup)
            ->where('itemmaster.financeCategoryMaster', 1)
            ->groupBy('erp_itemledger.itemSystemCode')
            //->take(50)
            ->get();


        $output = array(
            'warehouse' => $warehouse,
            'document' => $document,
            'segment' => $segment,
            'item' => $item,
            'customer' => $customerMaster
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    function getQSOQRY($request, $search = "")
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $approved_status = isset($request->approved_status)?$request->approved_status:null;
        $invoice_status = isset($request->invoice_status)?$request->invoice_status:null;
        $delivery_status = isset($request->delivery_status)?$request->delivery_status:null;

        $customers = (array)$request->customers;
        $customerSystemID = collect($customers)->pluck('customerCodeSystem')->toArray();

        $details = QuotationMaster::whereIn('companySystemID',$companyID)
            ->where('cancelledYN',0)
            ->whereIn('customerSystemCode',$customerSystemID)
                ->whereDate('createdDateTime', '>=', $fromDate)
                ->whereDate('createdDateTime', '<=', $toDate)
            ->where(function ($query) use($approved_status,$invoice_status,$delivery_status){

                if($approved_status != null){
                    if($approved_status == 1){
                        $query->where('confirmedYN',1);
                    }elseif ($approved_status == 2){
                        $query->where('approvedYN',0);
                    }elseif ($approved_status == 3){
                        $query->where('approvedYN',-1);
                    }
                }

                if($invoice_status != null){
                    if($invoice_status == 1){
                        $query->where('invoiceStatus',0);
                    }elseif ($invoice_status == 2){
                        $query->where('invoiceStatus',1);
                    }elseif ($invoice_status == 3){
                        $query->where('invoiceStatus',2);
                    }
                }

                if($delivery_status != null){
                    if($delivery_status == 1){
                        $query->where('deliveryStatus',0);
                    }elseif ($delivery_status == 2){
                        $query->where('deliveryStatus',1);
                    }elseif ($delivery_status == 3){
                        $query->where('deliveryStatus',2);
                    }
                }
            })
            ->with(['segment' => function($query){
                $query->select('serviceLineSystemID','ServiceLineCode','ServiceLineDes');
            },'detail'=> function($query){

                $query->with([
                    'invoice_detail' => function($q1){

                    $q1->with(['master'=> function($q2){

                        $q2->with(['receipt_detail' =>function($q3){
                            $q3->select('bookingInvCodeSystem','receiveAmountTrans');
                        }])
                            ->select('custInvoiceDirectAutoID');

                    }])
                    ->select('sellingTotal','customerItemDetailID','quotationDetailsID','custInvoiceDirectAutoID','custInvoiceDirectAutoID', 'VATAmount', 'qtyIssuedDefaultMeasure');

                },
                    'delivery_order_detail'=> function($q1){

                        $q1->with(['invoice_detail' => function($q2){

                            $q2->with(['master' => function($q3){

                                $q3->with(['receipt_detail' => function($q4){
                                    $q4->select('bookingInvCodeSystem','receiveAmountTrans');
                                }])
                                    ->select('custInvoiceDirectAutoID');
                            }])
                                ->select('sellingTotal','customerItemDetailID','quotationDetailsID','deliveryOrderDetailID','custInvoiceDirectAutoID',  'VATAmount', 'qtyIssuedDefaultMeasure');
                        }])
                            ->select('deliveryOrderDetailID','quotationDetailsID');

                    }
                ])
                    ->select('quotationDetailsID','quotationMasterID','transactionAmount', 'VATAmount', 'requestedQty');
            }])
            ->select('quotationMasterID','quotationCode','referenceNo','documentDate','serviceLineSystemID','customerName','transactionCurrency','transactionCurrencyDecimalPlaces','documentExpDate','confirmedYN','approvedYN','refferedBackYN','deliveryStatus','invoiceStatus','refferedBackYN','confirmedYN','approvedYN','is_return')
            ->get()
            ->toArray();

        $output = [];
        $x = 0;
        if(!empty($details) && $details != []){
            foreach ($details as $data){
                $output[$x]['quotationMasterID'] = isset($data['quotationMasterID'])?$data['quotationMasterID']:'';
                $output[$x]['quotationCode'] = isset($data['quotationCode'])?$data['quotationCode']:'';
                $output[$x]['documentDate'] = isset($data['documentDate'])?$data['documentDate']:'';
                $output[$x]['serviceLine'] = isset($data['segment']['ServiceLineDes'])?$data['segment']['ServiceLineDes']:'';
                $output[$x]['referenceNo'] = isset($data['referenceNo'])?$data['referenceNo']:'';
                $output[$x]['customer'] = isset($data['customerName'])?$data['customerName']:'';
                $output[$x]['currency'] = isset($data['transactionCurrency'])?$data['transactionCurrency']:'';
                $output[$x]['dp'] = isset($data['transactionCurrencyDecimalPlaces'])?$data['transactionCurrencyDecimalPlaces']:'';
                $output[$x]['documentExpDate'] = isset($data['documentExpDate'])?$data['documentExpDate']:'';
                $output[$x]['confirmedYN'] = isset($data['confirmedYN'])?$data['confirmedYN']:null;
                $output[$x]['approvedYN'] = isset($data['approvedYN'])?$data['approvedYN']:null;
                $output[$x]['refferedBackYN'] = isset($data['refferedBackYN'])?$data['refferedBackYN']:null;
                $output[$x]['customer_status'] = isset($data['quotationMasterID'])?QuotationStatus::getLastStatus($data['quotationMasterID']):'';
                $output[$x]['document_amount'] = 0;
                $output[$x]['invoice_amount'] = 0;
                $output[$x]['paid_amount'] = 0;
                $output[$x]['is_return'] = isset($data['is_return'])?$data['is_return']:0;
                $paid1 = 0;
                $paid2 = 0;
                $invoiceArray = [];
                if(isset($data['detail']) && count($data['detail'])> 0){
                    foreach ($data['detail'] as $qdetail){
                        $vatAmount = isset($qdetail['VATAmount']) ? ($qdetail['VATAmount'] * $qdetail['requestedQty']) : 0;
                        $output[$x]['document_amount'] += isset($qdetail['transactionAmount'])?($qdetail['transactionAmount']+$vatAmount):0;

                        // quotation -> delovery order -> invoice

                        if(isset($qdetail['delivery_order_detail']) && count($qdetail['delivery_order_detail'])> 0){

                            foreach ($qdetail['delivery_order_detail'] as $deliverydetail){

                                if(isset($deliverydetail['invoice_detail']) && count($deliverydetail['invoice_detail'])> 0){

                                    foreach ($deliverydetail['invoice_detail'] as $invoiceDetails){
                                        $invoiceArray[] = $invoiceDetails['custInvoiceDirectAutoID'];
                                        $vatAmount = isset($invoiceDetails['VATAmount']) ? ($invoiceDetails['VATAmount'] * $invoiceDetails['qtyIssuedDefaultMeasure']) : 0;
                                        $output[$x]['invoice_amount'] += isset($invoiceDetails['sellingTotal'])? ($invoiceDetails['sellingTotal']+ $vatAmount):0;

                                        if(isset($invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans']) && $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'] > 0){
                                            $paid1 = $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'];
                                        }

                                        /*$paymentsInvoice = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                            ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                            ->where('matchingDocID', 0)
                                            ->groupBy('custReceivePaymentAutoID')
                                            ->first();
                                        if(!empty($paymentsInvoice)){
                                            $output[$x]['paid_amount'] += $paymentsInvoice->receiveAmountTrans;
                                        }

                                        $paymentsInvoiceMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                            ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                            ->where('matchingDocID','>', 0)
                                            ->groupBy('custReceivePaymentAutoID')
                                            ->first();
                                        if(!empty($paymentsInvoiceMatch)){
                                            $output[$x]['paid_amount'] += $paymentsInvoiceMatch->receiveAmountTrans;
                                        }*/

                                    }
                                }

                            }
                        }

                        // quotation -> invoice
                        if(isset($qdetail['invoice_detail']) && count($qdetail['invoice_detail'])> 0){

                            foreach ($qdetail['invoice_detail'] as $invoiceDetails){
                                $invoiceArray[] = $invoiceDetails['custInvoiceDirectAutoID'];
                                $vatAmount = isset($invoiceDetails['VATAmount']) ? ($invoiceDetails['VATAmount'] * $invoiceDetails['qtyIssuedDefaultMeasure']) : 0;
                                $output[$x]['invoice_amount'] += isset($invoiceDetails['sellingTotal'])?($invoiceDetails['sellingTotal']+$vatAmount):0;
                                if(isset($invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans']) && $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'] > 0){
                                    $paid2 = $invoiceDetails['master']['receipt_detail'][0]['receiveAmountTrans'];
                                }

                                /*$paymentsInvoice = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                    ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                    ->where('matchingDocID', 0)
                                    ->groupBy('custReceivePaymentAutoID')
                                    ->first();
                                if(!empty($paymentsInvoice)){
                                    $output[$x]['paid_amount'] += $paymentsInvoice->receiveAmountTrans;
                                }

                                $paymentsInvoiceMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                    ->where('bookingInvCodeSystem', $invoiceDetails['custInvoiceDirectAutoID'])
                                    ->where('matchingDocID','>', 0)
                                    ->groupBy('custReceivePaymentAutoID')
                                    ->first();
                                if(!empty($paymentsInvoiceMatch)){
                                    $output[$x]['paid_amount'] += $paymentsInvoiceMatch->receiveAmountTrans;
                                }*/

                            }
                        }

                    }
                }

                // get paid amount
                $invoiceArray = array_unique($invoiceArray);
                if(!empty($invoiceArray) && count($invoiceArray)>0){
                    foreach ($invoiceArray as $invoice){
                        if($invoice > 0){
                            $paymentsInvoice = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                ->where('bookingInvCodeSystem', $invoice)
                                ->where('matchingDocID', 0)
                                ->groupBy('custReceivePaymentAutoID')
                                ->first();
                            if(!empty($paymentsInvoice)){
                                $output[$x]['paid_amount'] += $paymentsInvoice->receiveAmountTrans;
                            }

                            $paymentsInvoiceMatch = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountTrans) as receiveAmountTrans,matchingDocID,bookingInvCodeSystem')
                                ->where('bookingInvCodeSystem', $invoice)
                                ->where('matchingDocID','>', 0)
                                ->groupBy('custReceivePaymentAutoID')
                                ->first();
                            if(!empty($paymentsInvoiceMatch)){
                                $output[$x]['paid_amount'] += $paymentsInvoiceMatch->receiveAmountTrans;
                            }
                        }

                    }
                }
                $output[$x]['deliveryStatus'] = isset($data['deliveryStatus'])?$data['deliveryStatus']:0;
                $x++;
            }
        }
        return $output;

    }


    public function reportSoToReceipt(Request $request)
    {
        $input = $request->all();

        $customerID= $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');

        $salesOrder = $this->getSoToReceiptQry($input, $customerID);
        $data = \DataTables::of($salesOrder)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('quotationMasterID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->addColumn('deliveryOrder', function ($row) {
                return $this->getSOtoReceiptChainViaDeliveryOrder($row);
            })
            ->make(true);

        return $data;
    }

    public function getSOtoReceiptChainViaDeliveryOrder($row)
    {
        $deliveryOrders = DeliveryOrderDetail::selectRaw('sum(companyLocalAmount) as localAmount,
                                        sum(companyReportingAmount) as rptAmount,
                                        quotationMasterID,deliveryOrderID,deliveryOrderDetailID')
            ->where('quotationMasterID', $row->quotationMasterID)
            ->with(['master' => function ($query) {
                $query->with(['transaction_currency']);
            },'sales_return'=>function($query){
                $query->with('master');
            }])
            ->groupBy('deliveryOrderID')
            ->get();

        if (count($deliveryOrders) == 0) {
            $returnData['deliveryOrder'] = false;   
            $returnData['invoices'] = $this->getSOtoReceiptChainViaCustomerInvoice($row);

            return [$returnData];
        }

        foreach ($deliveryOrders as $do) {
            $invoices = CustomerInvoiceItemDetails::selectRaw('sum(issueCostLocalTotal) as localAmount,
                                                 sum(issueCostRptTotal) as rptAmount,custInvoiceDirectAutoID,deliveryOrderID')
                ->where('deliveryOrderID', $do->deliveryOrderID)
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custInvoiceDirectAutoID')
                ->get();

            foreach ($invoices as $invoice) {
                $recieptVouchers = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountLocal) as localAmount,
                                                 sum(receiveAmountRpt) as rptAmount,bookingInvCodeSystem,addedDocumentSystemID,matchingDocID, custReceivePaymentAutoID')
                    ->where('bookingInvCodeSystem', $invoice->custInvoiceDirectAutoID)
                    ->where('addedDocumentSystemID', 20)
                    ->where('matchingDocID', 0)
                    ->with(['master' => function ($query) {
                        $query->with(['currency']);
                    }])
                    ->groupBy('custReceivePaymentAutoID')
                    ->get();

                $totalInvoices = $recieptVouchers->toArray();

                $invoice->payments = $totalInvoices;
            }

            $do->invoices = $invoices->toArray();
        }

        return $deliveryOrders->toArray();
    }

    
    public function getSOtoReceiptChainViaCustomerInvoice($row)
    {
        $invoices = CustomerInvoiceItemDetails::selectRaw('sum(issueCostLocalTotal) as localAmount,
                                             sum(issueCostRptTotal) as rptAmount,custInvoiceDirectAutoID,deliveryOrderID')
            ->where('quotationMasterID', $row->quotationMasterID)
            ->with(['master' => function ($query) {
                $query->with(['currency']);
            }])
            ->groupBy('custInvoiceDirectAutoID')
            ->get();

        foreach ($invoices as $invoice) {
            $recieptVouchers = CustomerReceivePaymentDetail::selectRaw('sum(receiveAmountLocal) as localAmount,
                                             sum(receiveAmountRpt) as rptAmount,bookingInvCodeSystem,addedDocumentSystemID,matchingDocID, custReceivePaymentAutoID')
                ->where('bookingInvCodeSystem', $invoice->custInvoiceDirectAutoID)
                ->where('addedDocumentSystemID', 20)
                ->where('matchingDocID', 0)
                ->with(['master' => function ($query) {
                    $query->with(['currency']);
                }])
                ->groupBy('custReceivePaymentAutoID')
                ->get();

            $totalInvoices = $recieptVouchers->toArray();

            $invoice->payments = $totalInvoices;
        }

        return $invoices->toArray();
    }


    public function getSoToReceiptQry($request, $customerID)
    {
        $input = $request;
        $from = "";
        $to = "";

        if (array_key_exists('fromDate', $input) && $input['fromDate']) {
            $from = ((new Carbon($input['fromDate']))->format('Y-m-d'));
        }

        if (array_key_exists('toDate', $input) && $input['toDate']) {
            $to = ((new Carbon($input['toDate']))->format('Y-m-d'));
        }

        if (
            array_key_exists('toDate', $input) && array_key_exists('fromDate', $input) &&
            $input['toDate'] && $input['fromDate'] && $to <= $from
        ) {
            //$from = "";
            //$to = "";
        }

        $search = $input['search']['value'];
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
        }

        $purchaseOrder = QuotationMaster::where('companySystemID', $input['companyId'])
            ->where('cancelledYN',0)
            ->where('documentSystemID', 68)
            ->where('confirmedYN', 1)
            ->where('isDeleted', 0)
            ->where('approvedYN', -1)
            ->when($from && $to == "", function ($q) use ($from, $to) {
                return $q->where('approvedDate', '>=', $from);
            })
            ->when($from == "" && $to, function ($q) use ($from, $to) {
                return $q->where('approvedDate', '<=', $to);
            })
            ->when($from && $to, function ($q) use ($from, $to) {
                return $q->whereBetween('approvedDate', [$from, $to]);
            })
            ->when(request('customerID', false), function ($q) use ($input, $customerID) {
                return $q->whereIn('customerSystemCode', $customerID);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('quotationCode', 'LIKE', "%{$search}%")
                        ->orWhere('narration', 'LIKE', "%{$search}%");
                });
            })
            ->with(['customer']);

        return $purchaseOrder;
    }

    public function reportSoToReceiptFilterOptions(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];

        $customers = CustomerAssigned::where('companySystemID', $companyId);

        if (array_key_exists('search', $input)) {
            $search = $input['search'];
            $customers = $customers->where(function ($query) use ($search) {
                $query->where('CutomerCode', 'LIKE', "%{$search}%")
                    ->orWhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }


        $customers = $customers->take(15)->get(['companySystemID', 'CutomerCode', 'CustomerName', 'customerCodeSystem']);
        $output = array('customers' => $customers);

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }


    public function exportSoToReceiptReport(Request $request)
    {
        $input = $request->all();
        $data = array();
        $customerID= $request['customerID'];
        $customerID = (array)$customerID;
        $customerID = collect($customerID)->pluck('id');
        $output = ($this->getSoToReceiptQry($input, $customerID))->orderBy('quotationMasterID', 'DES')->get();

        foreach ($output as $row) {
            $row->deliveryOrders = $this->getSOtoReceiptChainViaDeliveryOrder($row);
        }

        $type = $request->type;
        if (!empty($output)) {
            $x = 0;
            foreach ($output as $value) {
                $data[$x]['Company ID'] = $value->companyID;
                $data[$x]['SO Number'] = $value->quotationCode;
                $data[$x]['SO Approved Date'] = \Helper::dateFormat($value->approvedDate);
                $data[$x]['Narration'] = $value->narration;
                if ($value->customer) {
                    $data[$x]['Customer Code'] = $value->customer->CutomerCode;
                    $data[$x]['Customer Name'] = $value->customer->CustomerName;
                } else {
                    $data[$x]['Customer Code'] = '';
                    $data[$x]['Customer Name'] = '';
                }
                $data[$x]['SO Amount'] = number_format($value->transactionAmount, 2);

                if (count($value->deliveryOrders) > 0) {
                    $grvMasterCount = 0;
                    foreach ($value->deliveryOrders as $grv) {
                        if ($grvMasterCount != 0) {
                            $x++;
                            $data[$x]['Company ID'] = '';
                            $data[$x]['SO Number'] = '';
                            $data[$x]['SO Approved Date'] = '';
                            $data[$x]['Narration'] = '';
                            $data[$x]['Customer Code'] = '';
                            $data[$x]['Customer Name'] = '';
                            $data[$x]['SO Amount'] = '';
                        }

                        if (isset($grv['master'])) {
                            $data[$x]['Delivery Code'] = $grv['master']['deliveryOrderCode'];
                            $data[$x]['Delivery Date'] = \Helper::dateFormat($grv['master']['deliveryOrderDate']);
                            $data[$x]['Delivery Amount'] = number_format($grv['rptAmount'], 2);
                        } else {
                            $data[$x]['Delivery Code'] = '';
                            $data[$x]['Delivery Date'] = '';
                            $data[$x]['Delivery Amount'] = '';
                        }


                        if (count($grv['invoices']) > 0) {
                            $invoicesCount = 0;
                            foreach ($grv['invoices'] as $invoice) {
                                if ($invoicesCount != 0) {
                                    $x++;
                                    $data[$x]['Company ID'] = '';
                                    $data[$x]['SO Number'] = '';
                                    $data[$x]['SO Approved Date'] = '';
                                    $data[$x]['Narration'] = '';
                                    $data[$x]['Customer Code'] = '';
                                    $data[$x]['Customer Name'] = '';
                                    $data[$x]['PO Amount'] = '';
                                    $data[$x]['Delivery Code'] = '';
                                    $data[$x]['Delivery Date'] = '';
                                    $data[$x]['Delivery Amount'] = '';
                                }

                                if ($invoice['master']) {
                                    $data[$x]['Invoice Code'] = $invoice['master']['bookingInvCode'];
                                    $data[$x]['Invoice Date'] = \Helper::dateFormat($invoice['master']['bookingDate']);
                                } else {
                                    $data[$x]['Invoice Code'] = '';
                                    $data[$x]['Invoice Date'] = '';
                                }
                                $data[$x]['Invoice Amount'] = number_format($invoice['rptAmount'], 2);

                                if (count($invoice['payments']) > 0) {
                                    $paymentsCount = 0;
                                    foreach ($invoice['payments'] as $payment) {
                                        if ($paymentsCount != 0) {
                                            $x++;
                                            $data[$x]['Company ID'] = '';
                                            $data[$x]['SO Number'] = '';
                                            $data[$x]['SO Approved Date'] = '';
                                            $data[$x]['Narration'] = '';
                                            $data[$x]['Customer Code'] = '';
                                            $data[$x]['Customer Name'] = '';
                                            $data[$x]['SO Amount'] = '';
                                            $data[$x]['Delivery Code'] = '';
                                            $data[$x]['Delivery Date'] = '';
                                            $data[$x]['Delivery Amount'] = '';
                                            $data[$x]['Invoice Code'] = '';
                                            $data[$x]['Invoice Date'] = '';
                                            $data[$x]['Invoice Amount'] = '';
                                        }

                                        if (!empty($payment['master'])) {
                                            $data[$x]['Receipt Code'] = $payment['master']['custPaymentReceiveCode'];
                                            $data[$x]['Receipt Date'] = \Helper::dateFormat($payment['master']['custPaymentReceiveDate']);
                                            $data[$x]['Receipt Posted Date'] = \Helper::dateFormat($payment['master']['postedDate']);
                                        } else {
                                            $data[$x]['Receipt Code'] = '';
                                            $data[$x]['Receipt Date'] = '';
                                            $data[$x]['Receipt Posted Date'] = '';
                                        }
                                       
                                        $data[$x]['Paid Amount'] = number_format($payment['rptAmount'], 2);
                                        $paymentsCount++;
                                    }
                                } else {
                                    $data[$x]['Receipt Code'] = '';
                                    $data[$x]['Receipt Date'] = '';
                                    $data[$x]['Receipt Posted Date'] = '';
                                    $data[$x]['Paid Amount'] = '';
                                }
                                $invoicesCount++;
                            }
                        } else {
                            $data[$x]['Invoice Code'] = '';
                            $data[$x]['Invoice Date'] = '';
                            $data[$x]['Invoice Amount'] = '';
                            $data[$x]['Receipt Code'] = '';
                            $data[$x]['Receipt Date'] = '';
                            $data[$x]['Receipt Posted Date'] = '';
                            $data[$x]['Paid Amount'] = '';
                        }
                        $grvMasterCount++;
                    }
                } else {
                    $data[$x]['Delivery Code'] = '';
                    $data[$x]['Delivery Date'] = '';
                    $data[$x]['Delivery Amount'] = '';
                    $data[$x]['Invoice Code'] = '';
                    $data[$x]['Invoice Date'] = '';
                    $data[$x]['Invoice Amount'] = '';
                    $data[$x]['Receipt Code'] = '';
                    $data[$x]['Receipt Date'] = '';
                    $data[$x]['Receipt Posted Date'] = '';
                    $data[$x]['Paid Amount'] = '';
                }
                $x++;
            }
        }

        $companyMaster = Company::find(isset($request->companyId)?$request->companyId:null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );


        $fileName = 'so_to_receipt';
        $path = 'sales/report/so_to_receipt/excel/';
        $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }

    }

}
