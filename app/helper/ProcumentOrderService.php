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
use App\Models\SupplierMaster;
use App\Models\ItemMaster;
use App\Models\ProcumentOrder;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseOrderDetails;
use App\Models\User;
use App\Models\Employee;
use App\Models\PurchaseRequest;
use App\helper\CommonJobService;
use App\Models\PurchaseRequestDetails;
use Illuminate\Support\Facades\DB;
use Response;
use App\Repositories\SegmentAllocatedItemRepository;

use Illuminate\Support\Facades\Log;

class ProcumentOrderService
{
    private $purchaseRequestDetailsRepository;
    private $segmentAllocatedItemRepository;
    
    public function __construct(PurchaseRequestDetailsRepository $purchaseRequestDetailsRepo, SegmentAllocatedItemRepository $segmentAllocatedItemRepository)
    {
        $this->$segmentAllocatedItemRepository = $segmentAllocatedItemRepository;
    }

    public static function  addMultipleItems($records,$purchaseOrder,$db,$authID) {

        CommonJobService::db_switch($db);

        $items = $records;
        $valiatedItems = self::validateItem($items,$purchaseOrder,$authID);
        $procumentOrder = ProcumentOrder::find($purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 0;
        $procumentOrder->save();
        self::allocateSegments($valiatedItems,$procumentOrder['documentSystemID'],$authID);
        Log::info('Add Mutiple Items End');
        $procumentOrder = ProcumentOrder::find($purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 1;
        $procumentOrder->save();
    }

    public static function allocateSegments($items,$documentSystemID,$authID) {
        foreach($items as $item) {
            $procumentOrderDetails = PurchaseOrderDetails::create($item);

            $allocationData = [
                'serviceLineSystemID' =>  $procumentOrderDetails['serviceLineSystemID'],
                'documentSystemID' => $documentSystemID,
                'docAutoID' =>  $procumentOrderDetails['purchaseOrderMasterID'],
                'docDetailID' => $procumentOrderDetails['purchaseOrderDetailsID']
            ];

            $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', $allocationData['serviceLineSystemID'])
            ->where('documentSystemID', $allocationData['documentSystemID'])
            ->where('documentMasterAutoID', $allocationData['docAutoID'])
            ->where('documentDetailAutoID', $allocationData['docDetailID'])
            ->first();

            if ($checkAlreadyAllocated) {
                return ['status' => false, 'message' => 'Item already allocated for selected segment'];
            }

            $procumentOrder = ProcumentOrder::find($allocationData['docAutoID']);

            $itemData = PurchaseOrderDetails::find($allocationData['docDetailID']);

            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $allocationData['documentSystemID'])
            ->where('documentMasterAutoID', $allocationData['docAutoID'])
            ->where('documentDetailAutoID', $allocationData['docDetailID'])
            ->sum('allocatedQty');

            $allocationData = [
                'documentSystemID' => $allocationData['documentSystemID'],
                'documentMasterAutoID' => $allocationData['docAutoID'],
                'documentDetailAutoID' => $allocationData['docDetailID'],
                'detailQty' => $itemData->noQty,
                'allocatedQty' => $itemData->noQty - $allocatedQty,
                'serviceLineSystemID' => $allocationData['serviceLineSystemID']
            ];

            $createRes = SegmentAllocatedItem::create($allocationData);

        }
    }

    public static function validateItem($items,$purchaseOrder,$authID) {
        $validatedItemsArray = [];
        foreach($items as $item) {
            if(array_key_exists('item_code',$item)) {
                $orgItem = ItemMaster::where('primaryCode',trim($item['item_code']))->first();
                if($orgItem) {
                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $purchaseOrder['companySystemID'])
                    ->where('mainItemCategoryID', $orgItem['financeCategoryMaster'])
                    ->where('itemCategorySubID', $orgItem['financeCategorySub'])
                    ->first();

                    $item['purchaseOrderMasterID'] = $purchaseOrder['purchaseOrderID'];
                    $item['companyID'] = $purchaseOrder['companyID'];
                    $item['companySystemID'] = $purchaseOrder['companySystemID'];
                    $item['serviceLineSystemID'] = $purchaseOrder['serviceLineSystemID'];
                    $item['serviceLineCode'] = $purchaseOrder['serviceLine'];
                    $item['itemCode'] = $orgItem['itemCodeSystem'];
                    $item['unitCost'] = trim($item['unit_cost']);
                    $item['noQty'] = trim($item['no_qty']);
                    $item['itemPrimaryCode'] = trim($orgItem['primaryCode']);
                    $item['itemDescription'] = trim($orgItem['itemDescription']);
                    $item['netAmount'] =   $item['unitCost'] * $item['noQty'];
                    $item['itemFinanceCategoryID'] = $orgItem['financeCategoryMaster'];
                    $item['itemFinanceCategorySubID'] = $orgItem['financeCategorySub'];

                    $supplier = SupplierMaster::find($purchaseOrder['supplierID']);
                    $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $purchaseOrder['supplierID'])
                    ->where('isDefault', -1)
                    ->first();

                    if ($supplierCurrency) {
                        $item['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
                        $item['supplierTransactionER'] = 1;
                    }

                    $currencyConversionDefaultMaster = \Helper::currencyConversion($purchaseOrder['companySystemID'], $purchaseOrder['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);

                    if ($currencyConversionDefaultMaster) {
                        $item['supplierDefaultER'] = $currencyConversionDefaultMaster['transToDocER'];
                    }

                    $item['supplierPartNumber'] = trim($orgItem['secondaryItemCode']);
                    unset($item['item_code'], $item['unit_cost'], $item['no_qty']);
                    $item['unitOfMeasure'] = trim($orgItem['unit']);
                    $item['altUnit'] = trim($orgItem['unit']);
                    $item['altUnitValue'] = trim($item['noQty']);
					$item['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
					$item['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
					$item['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
					$item['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                    $item['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;

                    $item['supplierItemCurrencyID'] = $purchaseOrder['supplierTransactionCurrencyID'];
                    $item['foreignToLocalER'] = $purchaseOrder['supplierTransactionER'];
        
                    $item['supplierDefaultCurrencyID'] = $purchaseOrder['supplierDefaultCurrencyID'];
                    $item['supplierDefaultER'] = $purchaseOrder['supplierDefaultER'];
        
                    $item['companyReportingER'] = $purchaseOrder['companyReportingER'];
                    $item['localCurrencyER'] = $purchaseOrder['localCurrencyER'];
                    $poDate = now();
                    $item['budgetYear'] = CompanyFinanceYear::budgetYearByDate($poDate, $purchaseOrder['companySystemID']);
                    $company = Company::where('companySystemID', $purchaseOrder['companySystemID'])->first();

                    $item['companyReportingCurrencyID'] = $company->reportingCurrency;

                    $item['departmentID'] = 'PROC';
                    $user = User::find($authID);
                    if($user) {
                        $employee = Employee::with(['profilepic'])->find($user->employee_id);
                        $item['createdPcID'] = gethostname();
                        $item['createdUserID'] = $employee->empID;
                        $item['createdUserSystemID'] = $employee->employeeSystemID;
                    }
                    $item["timestamp"] = date('Y-m-d H:i:s');
                    array_push($validatedItemsArray,$item);
                }
            }
        }

        return $validatedItemsArray;
    }

}