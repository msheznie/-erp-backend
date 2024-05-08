<?php
/**
 * =============================================
 * -- File Name : inventory.php
 * -- Project Name : ERP
 * -- Module Name :  email class
 * -- Author : Mohamed Fayas
 * -- Create date : 15 - August 2018
 * -- Description : This file contains the all the common inventory function
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Repositories\PurchaseRequestDetailsRepository;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\ErpItemLedger;
use App\Models\AssetFinanceCategory;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\SupplierCurrency;
use App\Models\Unit;
use App\Models\SupplierMaster;
use App\Models\ItemMaster;
use App\Models\ProcumentOrder;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseOrderDetails;
use App\Models\User;
use App\Models\Employee;
use App\Models\PurchaseRequest;
use App\helper\CommonJobService;
use App\Models\QuotationDetails;
use App\Models\PurchaseRequestDetails;
use Illuminate\Support\Facades\DB;
use Response;
use App\Repositories\QuotationDetailsRepository;
use Carbon\Carbon;

use Illuminate\Support\Facades\Log;

class QuotationAddMultipleItemsService
{
    private $purchaseRequestDetailsRepository;
    private $quotationDetailsRepository;
    
    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo, QuotationDetailsRepository $quotationDetailsRepository)
    {
        $this->$quotationDetailsRepository = $quotationDetailsRepository;
    }

    public static function  addMultipleItems($records,$quotation,$db,$authID) {

        $items = $records;
        $itemsToUpload = array();
        // $employee = \Helper::getEmployeeInfo();
        Log::useFiles(storage_path() . '/logs/sales_order_jobs.log');

        foreach($items as $item) {
            $data = array();
            $orgItem  = ItemMaster::where('primaryCode', $item['item_code'])->first();
            if($orgItem)
            {

                if((is_numeric($item['qty']) && $item['qty'] != 0)  &&  (is_numeric($item['sales_price']) && $item['sales_price'] != 0)  &&  is_numeric($item['discount'])) {

                    $itemAssigned = ItemAssigned::where('itemCodeSystem', $orgItem->itemCodeSystem)
                        ->where('companySystemID', $quotation['companySystemID'])
                        ->first();
                    $company = Company::find($quotation['companySystemID']);

                    $unit  = Unit::find($orgItem->unit);
                    $data = [
                        'itemAutoID' => $orgItem->itemCodeSystem,
                        'itemSystemCode' => $item['item_code'],
                        'itemDescription' => $orgItem->itemDescription,
                        'itemCategory' => $orgItem->financeCategoryMaster,
                        'defaultUOMID' => $orgItem->unit,
                        'unitOfMeasureID' => $orgItem->unit,
                        'defaultUOM' => $orgItem->unit,
                        'unitOfMeasure' => ($unit) ? $unit->UnitShortCode : null,
                        'itemReferenceNo' => $orgItem->secondaryItemCode,
                        'comment' => (isset($item['comments'])) ? $item['comments'] :  '',
                        'companySystemID' => $company->companySystemID,
                        'companyID' => $company->CompanyID
                    ];

                    $currencyConversion = \Helper::currencyConversion($quotation['companySystemID'], $quotation['transactionCurrencyID'], $quotation['transactionCurrencyID'], $quotation['transactionAmount']);
                    $data['companyLocalAmount'] = \Helper::roundValue($currencyConversion['localAmount']);
                    $data['companyReportingAmount'] = \Helper::roundValue($currencyConversion['reportingAmount']);

                    $currencyConversionDefault = \Helper::currencyConversion($quotation['companySystemID'], $quotation['customerCurrencyID'], $quotation['customerCurrencyID'], $quotation['transactionAmount']);

                    $data['customerAmount'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);
                    $data['wacValueLocal'] = $itemAssigned->wacValueLocal;



                    $data['modifiedDateTime'] = Carbon::now();
                    $data['modifiedPCID'] = gethostname();
                    $data['quotationMasterID'] = $quotation['quotationMasterID'];
                    $data['requestedQty'] = $item['qty'];
                    $data['unittransactionAmount'] = $item['sales_price'];


                    if($item['discount']) {
                        $data['discountPercentage'] =  number_format((($item['discount']  * 100) / ($data['unittransactionAmount'])),3);
                        $data['discountAmount'] = $item['discount'];
                    }else {
                        $data['discountPercentage'] = 0;
                        $data['discountAmount'] = 0;
                    }

                    $currencyConversionVAT = \Helper::currencyConversion($quotation['companySystemID'], $quotation['transactionCurrencyID'], $quotation['transactionCurrencyID'], $item['vat']);
                    if($quotation['isVatEligible']) {
                        $data['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                        $data['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                        $data['VATAmount'] = \Helper::roundValue((($data['unittransactionAmount']) - $data['discountAmount']) * ($item['vat'] / 100));
                    }else {
                        $data['VATAmountLocal'] = 0;
                        $data['VATAmountRpt']  = 0;
                        $data['VATAmount'] = 0;
                    }

                    $totalNetcost = $item['qty'] * (($data['unittransactionAmount']) - $data['discountAmount']);
                    $data['VATPercentage'] = $item['vat'];

                    $data['transactionAmount'] = \Helper::roundValue($totalNetcost);
                    // $item['modifiedUserID'] = $employee->empID;
                    // $item['modifiedUserName'] = $employee->empName;
                        array_push($itemsToUpload,$data);
                }

            }
           

        }

        QuotationDetails::insert($itemsToUpload);
        Log::info($data);
        
    }

    


}
