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

use App\Models\ErpProjectMaster;
use App\Models\PoBulkUploadErrorLog;
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

    public static function  addMultipleItems($items,$purchaseOrder,$db,$authID) {

        CommonJobService::db_switch($db);
        $valiatedItems = self::uploadValidations($items, $purchaseOrder, $authID);
        //$valiatedItems = self::validateItem($items,$purchaseOrder,$authID);
        $procumentOrder = ProcumentOrder::find($purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 0;
        $procumentOrder->save();

        if (!empty($valiatedItems['itemDetails'])) {
            self::allocateSegments($valiatedItems['itemDetails'], $procumentOrder['documentSystemID'],$authID);
        }

        if (!empty($valiatedItems['errorLog'])) {
            self::errorLogUpdate($valiatedItems['errorLog'], $procumentOrder['purchaseOrderID']);
        }

        Log::info('Add Multiple Items End');
        $procumentOrder = ProcumentOrder::find($purchaseOrder['purchaseOrderID']);
        $procumentOrder->upload_job_status = 1;
        $procumentOrder->isBulkItemJobRun = 0;
        $procumentOrder->successDetailsCount = $valiatedItems['successCount'];
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
                    if($financeItemCategorySubAssigned) {
                        $item['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                        $item['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                        $item['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                        $item['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                        $item['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                    }

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

    public static function uploadValidations($excelRows, $purchaseOrder, $authID)
    {
        $rowNumber = 2;
        $validationErrorMsg = $validatedItemsArray = [];
        $successCount = 0;

        foreach ($excelRows as $rowData) {
            $isValidationError = 0;
            if (!array_key_exists('no_qty',$rowData) || is_null($rowData['no_qty'])) {
                $validationErrorMsg[] = 'The item Qty has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else if (!is_numeric($rowData['no_qty'])) {
                $validationErrorMsg[] = 'The quantity should be a numeric value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else if ($rowData['no_qty'] < 0) {
                $validationErrorMsg[] = 'The quantity should be a positive value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if (!array_key_exists('unit_cost',$rowData) || is_null($rowData['unit_cost'])) {
                $validationErrorMsg[] = 'The Unit Cost has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else if (!is_numeric($rowData['unit_cost'])) {
                $validationErrorMsg[] = 'The Unit cost should be a numeric value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } if ($rowData['unit_cost'] < 0) {
                $validationErrorMsg[] = 'The Unit cost should be a positive value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if (array_key_exists('dis_percentage',$rowData) && $rowData['dis_percentage'] !== null) {
                if($rowData['dis_percentage'] < 0 || $rowData['dis_percentage'] > 100) {
                    $validationErrorMsg[] = 'The Dis Percentage value should be between 0 - 100 for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                } else if (!is_numeric($rowData['dis_percentage'])) {
                    $validationErrorMsg[] = 'The Dis Percentage should be a numeric value for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }

            if (array_key_exists('vat_percentage',$rowData) && $rowData['vat_percentage'] !== null) {
                if($rowData['vat_percentage'] < 0 || $rowData['vat_percentage'] > 100) {
                    $validationErrorMsg[] = 'The VAT Percentage value should be between 0 - 100 for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                } else if (!is_numeric($rowData['vat_percentage'])) {
                    $validationErrorMsg[] = 'The VAT Percentage should be a numeric value for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }

            if (array_key_exists('project',$rowData) && $rowData['project'] !== null) {
                $projectId = ErpProjectMaster::where('projectCode', trim($rowData['project']))
                    ->pluck('id')
                    ->first();
                if (!$projectId) {
                    $validationErrorMsg[] = 'The Project Code not match with system for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }

            if (!array_key_exists('item_code',$rowData) || is_null($rowData['item_code'])) {
                $validationErrorMsg[] = 'The item code has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else {
                $categoryType = ItemMaster::where('primaryCode', trim($rowData['item_code']))
                    ->pluck('categoryType')
                    ->first();

                if ($categoryType) {
                    $purchaseCategoryTypes = [
                        '[{"id":1,"itemName":"Purchase"}]',
                        '[{"id":1,"itemName":"Purchase"},{"id":2,"itemName":"Sale"}]',
                        '[{"id":2,"itemName":"Sale"},{"id":1,"itemName":"Purchase"}]'
                    ];
                    if (!in_array($categoryType, $purchaseCategoryTypes)) {
                        $validationErrorMsg[] = 'The inventory items added should only be of Item Type: Purchase or Purchase & Sales for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }
                } else {
                    $validationErrorMsg[] = 'The item code does not match with a system for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }

            if($isValidationError == 0) {
                $orgItem = ItemMaster::where('primaryCode',trim($rowData['item_code']))->first();
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
                    $item['itemPrimaryCode'] = trim($orgItem['primaryCode']);
                    $item['itemDescription'] = trim($orgItem['itemDescription']);
                    $item['itemFinanceCategoryID'] = $orgItem['financeCategoryMaster'];
                    $item['itemFinanceCategorySubID'] = $orgItem['financeCategorySub'];
                    $item['itemCode'] = $orgItem['itemCodeSystem'];
                    $item['comment'] = isset($rowData['comments']) ? $rowData['comments'] : '';
                    $item['clientReferenceNumber'] = isset($rowData['client_ref_no']) ? $rowData['client_ref_no'] : '';
                    $item['detail_project_id'] = isset($projectId) ? $projectId : '';

                    $item['unitCost'] = trim($rowData['unit_cost']);
                    $item['noQty'] = trim($rowData['no_qty']);

                    /** Discount logic */
                    $item['discountAmount'] = 0;
                    $item['discountPercentage'] = 0;
                    if (array_key_exists('dis_percentage',$rowData) && $rowData['dis_percentage'] !== null) {
                        $item['discountPercentage'] = $rowData['dis_percentage'];
                        if ($rowData['dis_percentage'] > 0) {
                            $discountAmount = ($item['unitCost'] / 100) * $rowData['dis_percentage'];
                            $item['discountAmount'] = \Helper::roundValue($discountAmount);
                        }
                    }

                    $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $purchaseOrder['supplierID'])->where('isDefault', -1)->first();
                    if ($supplierCurrency) {
                        $item['supplierDefaultCurrencyID'] = $supplierCurrency->currencyID;
                        $item['supplierTransactionER'] = 1;
                    }

                    /** VAT logic */
                    $item['VATAmount'] = 0;
                    $item['VATPercentage'] = 0;
                    if (array_key_exists('vat_percentage',$rowData) && $rowData['vat_percentage'] !== null) {
                        $item['VATPercentage'] = $rowData['vat_percentage'];

                        if ($rowData['vat_percentage'] > 0) {
                            $item['VATAmount'] = (($item['unitCost'] / 100) * $rowData['vat_percentage']);

                            $currencyConversionVAT = \Helper::currencyConversion($purchaseOrder['companySystemID'], $purchaseOrder['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, $item['VATAmount']);
                            $item['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
                            $item['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
                        }
                    }

                    $item['netAmount'] =   ($item['unitCost'] - $item['discountAmount'] + $item['VATAmount']) * $item['noQty'];

                    $currencyConversionDefaultMaster = \Helper::currencyConversion($purchaseOrder['companySystemID'], $purchaseOrder['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);
                    if ($currencyConversionDefaultMaster) {
                        $item['supplierDefaultER'] = $currencyConversionDefaultMaster['transToDocER'];
                    }

                    $item['supplierPartNumber'] = trim($orgItem['secondaryItemCode']);
                    $item['unitOfMeasure'] = trim($orgItem['unit']);
                    $item['altUnit'] = trim($orgItem['unit']);
                    $item['altUnitValue'] = trim($item['noQty']);
                    if($financeItemCategorySubAssigned) {
                        $item['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
                        $item['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                        $item['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                        $item['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                        $item['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                    }

                    $item['supplierItemCurrencyID'] = $purchaseOrder['supplierTransactionCurrencyID'];
                    $item['foreignToLocalER'] = $purchaseOrder['supplierTransactionER'];

                    $item['supplierDefaultCurrencyID'] = $purchaseOrder['supplierDefaultCurrencyID'];
                    $item['supplierDefaultER'] = $purchaseOrder['supplierDefaultER'];

                    $item['companyReportingER'] = $purchaseOrder['companyReportingER'];
                    $item['localCurrencyER'] = $purchaseOrder['localCurrencyER'];
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
                $successCount += 1;
            }
            $rowNumber++;
        }

        $data = [
            'itemDetails' => $validatedItemsArray,
            'errorLog' => $validationErrorMsg,
            'successCount' => $successCount
        ];
        return $data;
    }

    public static function errorLogUpdate($errorData, $documentSystemId)
    {
        foreach ($errorData as $details) {
            $insertError = [
                'documentSystemID' => $documentSystemId,
                'error' => $details
            ];
            PoBulkUploadErrorLog::create($insertError);
        }
    }
}
