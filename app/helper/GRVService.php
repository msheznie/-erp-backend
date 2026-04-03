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
use App\Models\GRVBulkUploadErrorLog;
use App\Models\Company;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemMasterCategoryType;
use App\Models\ItemCategoryTypeMaster;
use App\Models\SegmentAllocatedItem;
use App\Models\ItemAssigned;
use App\Models\SupplierCurrency;
use App\Models\ItemMaster;
use App\Models\GRVMaster;
use App\Models\CompanyFinanceYear;
use App\Models\User;
use App\Models\Employee;
use App\Models\Unit;
use App\helper\CommonJobService;
use App\Repositories\SegmentAllocatedItemRepository;
use App\helper\Helper;

class GRVService
{
    private $segmentAllocatedItemRepository;
    public function __construct(SegmentAllocatedItemRepository $segmentAllocatedItemRepository)
    {
        $this->$segmentAllocatedItemRepository = $segmentAllocatedItemRepository;
    }

    public static function  addMultipleItems($items,$grvMaster,$db,$authID) {

        CommonJobService::db_switch($db);
        $valiatedItems = self::uploadValidations($items, $grvMaster, $authID);
        $grvMaster = GRVMaster::find($grvMaster['grvAutoID']);
        $grvMaster->upload_job_status = 0;
        $grvMaster->successDetailsCount = 0;
        $grvMaster->excelRowCount = 0;
        $grvMaster->save();

        if (!empty($valiatedItems['itemDetails'])) {
            self::allocateSegments($valiatedItems['itemDetails'], $grvMaster['documentSystemID'],$authID);
        }

        if (!empty($valiatedItems['errorLog'])) {
            self::errorLogUpdate($valiatedItems['errorLog'], $grvMaster['grvAutoID']);
        }

        $grvMaster = GRVMaster::find($grvMaster['grvAutoID']);
        $grvMaster->upload_job_status = 1;
        $grvMaster->isBulkItemJobRun = 0;
        $grvMaster->successDetailsCount = $valiatedItems['successCount'];
        $grvMaster->excelRowCount = $valiatedItems['excelRowCount'];
        $grvMaster->save();
    }

    public static function allocateSegments($items,$documentSystemID,$authID) 
    {
        foreach($items as $item) {
            $grvDetails = GRVDetails::create($item);

            $allocationData = [
                'serviceLineSystemID' =>  $grvDetails['serviceLineSystemID'],
                'documentSystemID' => $documentSystemID,
                'grvAutoID' =>  $grvDetails['grvAutoID'],
                'grvDetailsID' => $grvDetails['grvDetailsID']
            ];

            $checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', $allocationData['serviceLineSystemID'])
            ->where('documentSystemID', $allocationData['documentSystemID'])
            ->where('documentMasterAutoID', $allocationData['grvAutoID'])
            ->where('documentDetailAutoID', $allocationData['grvDetailsID'])
            ->first();

            if ($checkAlreadyAllocated) {
                continue;
            }

            $itemData = GRVDetails::find($allocationData['grvDetailsID']);

            $allocatedQty = SegmentAllocatedItem::where('documentSystemID', $allocationData['documentSystemID'])
            ->where('documentMasterAutoID', $allocationData['grvAutoID'])
            ->where('documentDetailAutoID', $allocationData['grvDetailsID'])
            ->sum('allocatedQty');

            $allocationData = [
                'documentSystemID' => $allocationData['documentSystemID'],
                'documentMasterAutoID' => $allocationData['grvAutoID'],
                'documentDetailAutoID' => $allocationData['grvDetailsID'],
                'detailQty' => $itemData->noQty,
                'allocatedQty' => $itemData->noQty - $allocatedQty,
                'serviceLineSystemID' => $allocationData['serviceLineSystemID']
            ];

            $createRes = SegmentAllocatedItem::create($allocationData);

        }
    }
    
    public static function uploadValidations($excelRows, $grvMaster, $authID)
    {
        $rowNumber = 2;
        $validationErrorMsg = $validatedItemsArray = [];
        $successCount = $excelRowCount = 0;
        $companyId = $grvMaster['companySystemID'];
       

        foreach ($excelRows as $rowData) {
            $isValidationError = 0;
            $projectId = null;
            $normalizedItemCode = trim((string) ($rowData['item_code'] ?? ''));
            $qtyRaw = trim((string) ($rowData['net_qty'] ?? ''));
            $unitCostRaw = trim((string) ($rowData['unit_cost'] ?? ''));
            $uploadedUomRaw = trim((string) ($rowData['uom'] ?? $rowData['unit_of_measure'] ?? ''));
            $orgItem = null;
            $orgItemUnit = null;
            $itemUomKey = null;

            // Mandatory columns validation.
            if ($normalizedItemCode === '') {
                $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code', 'Value is mandatory');
                $isValidationError = 1;
            }
            if ($qtyRaw === '') {
                $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Net Quantity', 'Value is mandatory');
                $isValidationError = 1;
            }
            if ($unitCostRaw === '') {
                $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Unit Cost', 'Value is mandatory');
                $isValidationError = 1;
            }

            if ($qtyRaw !== '') {
                if (!is_numeric($qtyRaw)) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Net Quantity', 'Must be numeric');
                    $isValidationError = 1;
                } elseif ((float) $qtyRaw <= 0) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Net Quantity', 'Must be greater than 0');
                    $isValidationError = 1;
                }
            }

            $currencyPrecision = isset($grvMaster['supplierTransactionCurrencyID'])
            ? (int) Helper::getCurrencyDecimalPlace($grvMaster['supplierTransactionCurrencyID'])
            : 2;
            $currencyPrecision = $currencyPrecision >= 0 ? $currencyPrecision : 2;

            if ($unitCostRaw !== '') {
                if (!is_numeric($unitCostRaw)) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Unit Cost', 'Must be numeric');
                    $isValidationError = 1;
                } elseif ((float) $unitCostRaw <= 0) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Unit Cost', 'Must be greater than 0');
                    $isValidationError = 1;
                } elseif (self::countDecimalPlaces($unitCostRaw) > $currencyPrecision) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError(
                        $rowNumber,
                        'Unit Cost',
                        'Decimal places exceed currency precision of ' . $currencyPrecision
                    );
                    $isValidationError = 1;
                }
            }

            if (array_key_exists('vat_percentage',$rowData) && $rowData['vat_percentage'] !== null) {
                if($rowData['vat_percentage'] < 0 || $rowData['vat_percentage'] > 100) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'VAT Percentage', 'Must be between 0 and 100');
                    $isValidationError = 1;
                } else if (!is_numeric($rowData['vat_percentage'])) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'VAT Percentage', 'Must be numeric');
                    $isValidationError = 1;
                }
            }

            if (array_key_exists('project',$rowData) && $rowData['project'] !== null) {
                $projectId = ErpProjectMaster::where('projectCode', $rowData['project'])
                    ->pluck('id')
                    ->first();
                if (!$projectId) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Project', 'Project code is invalid');
                    $isValidationError = 1;
                }
            }

            if ($normalizedItemCode !== '') {
                $orgItem = ItemMaster::where('primaryCode', $normalizedItemCode)->first();
                if (!$orgItem) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code', 'Item code is invalid');
                    $isValidationError = 1;
                } else {
                    $isAssigned = ItemAssigned::where('itemCodeSystem', $orgItem->itemCodeSystem)
                        ->where('companySystemID', $companyId)
                        ->where('isAssigned', -1)
                        ->exists();
                    if (!$isAssigned) {
                        $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code', 'Item is not assigned to the company');
                        $isValidationError = 1;
                    }
                    if ((int) $orgItem->itemApprovedYN !== 1) {
                        $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code', 'Item is not fully approved');
                        $isValidationError = 1;
                    }
                    if ((int) $orgItem->isActive !== 1) {
                        $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code', 'Item is inactive');
                        $isValidationError = 1;
                    }

                    $purchaseAllowed = ItemMasterCategoryType::whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems())
                        ->where('itemCodeSystem', $orgItem->itemCodeSystem)
                        ->exists();
                    if (!$purchaseAllowed) {
                        $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Type', 'Only Purchase or Purchase & Sales items are allowed');
                        $isValidationError = 1;
                    }

                    $orgItemUnit = Unit::find($orgItem->unit);
                    if (!$orgItemUnit) {
                        $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'UOM', 'Invalid item UOM configuration');
                        $isValidationError = 1;
                    } else {
                        if (is_numeric($qtyRaw) && self::countDecimalPlaces($qtyRaw) > (int) ($orgItemUnit->decimalPrecision ?? 2)) {
                            $validationErrorMsg[] = self::buildRowColumnReasonError(
                                $rowNumber,
                                'Quantity',
                                'Decimal places exceed UOM precision of ' . (int) ($orgItemUnit->decimalPrecision ?? 2)
                            );
                            $isValidationError = 1;
                        }
                        if ($uploadedUomRaw !== '') {
                            $uomMatched = strcasecmp($uploadedUomRaw, (string) $orgItemUnit->UnitShortCode) === 0
                                || strcasecmp($uploadedUomRaw, (string) $orgItemUnit->UnitDes) === 0
                                || (is_numeric($uploadedUomRaw) && (int) $uploadedUomRaw === (int) $orgItemUnit->UnitID);
                            if (!$uomMatched) {
                                $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'UOM', 'UOM is invalid for selected item');
                                $isValidationError = 1;
                            }
                        }
                    }
                }
            }
            $uploadedItemUomKeys = [];
            if($isValidationError == 0) {
                $existingItemUomKeys = GRVDetails::where('grvAutoID', $grvMaster['grvAutoID'])
                ->select('itemCode', 'unitOfMeasure')
                ->get()
                ->mapWithKeys(function ($detail) {
                    return [trim((string) $detail->itemCode) . '_' . trim((string) $detail->unitOfMeasure) => true];
                })
                ->toArray();
                $itemUomKey = trim((string) $orgItem->itemCodeSystem) . '_' . trim((string) $orgItem->unit);

                if (isset($existingItemUomKeys[$itemUomKey])) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code/UOM', 'Duplicate item and UOM already exists in GRV');
                    $isValidationError = 1;
                } elseif (isset($uploadedItemUomKeys[$itemUomKey])) {
                    $validationErrorMsg[] = self::buildRowColumnReasonError($rowNumber, 'Item Code/UOM', 'Duplicate item and UOM found in uploaded file');
                    $isValidationError = 1;
                }
            }
            
            if($isValidationError == 0) {
                if($orgItem) {
                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $grvMaster['companySystemID'])
                        ->where('mainItemCategoryID', $orgItem['financeCategoryMaster'])
                        ->where('itemCategorySubID', $orgItem['financeCategorySub'])
                        ->first();

                    $item['grvAutoID'] = $grvMaster['grvAutoID'];
                    $item['companyID'] = $grvMaster['companyID'];
                    $item['companySystemID'] = $grvMaster['companySystemID'];
                    $item['serviceLineSystemID'] = $grvMaster['serviceLineSystemID'];
                    $item['serviceLineCode'] = $grvMaster['serviceLineCode'];
                    $item['itemPrimaryCode'] = trim($orgItem['primaryCode']);
                    $item['itemDescription'] = trim($orgItem['itemDescription']);
                    $item['itemFinanceCategoryID'] = $orgItem['financeCategoryMaster'];
                    $item['itemFinanceCategorySubID'] = $orgItem['financeCategorySub'];
                    $item['itemCode'] = $orgItem['itemCodeSystem'];
                    $item['comment'] = isset($rowData['comments']) ? $rowData['comments'] : '';
                    $item['clientReferenceNumber'] = isset($rowData['client_ref_no']) ? $rowData['client_ref_no'] : '';
                    $item['detail_project_id'] = isset($projectId) ? $projectId : '';

                    $item['unitCost'] = trim($rowData['unit_cost']);
                    $item['noQty'] = trim($rowData['net_qty']);

                    /** Discount logic */
                    $item['discountAmount'] = 0;
                    $item['discountPercentage'] = 0;
                    if (array_key_exists('dis_percentage',$rowData) && $rowData['dis_percentage'] !== null) {
                        $item['discountPercentage'] = $rowData['dis_percentage'];
                        if ($rowData['dis_percentage'] > 0) {
                            $discountAmount = ($item['unitCost'] / 100) * $rowData['dis_percentage'];
                            $item['discountAmount'] = Helper::roundValue($discountAmount);
                        }
                    }

                    $supplierCurrency = SupplierCurrency::where('supplierCodeSystem', $grvMaster['supplierID'])->where('isDefault', -1)->first();
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
                            $item['VATAmount'] = ((($item['unitCost'] - $item['discountAmount']) / 100) * $rowData['vat_percentage']);

                            $currencyConversionVAT = Helper::currencyConversion($grvMaster['companySystemID'], $grvMaster['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, $item['VATAmount']);
                            $item['VATAmountLocal'] = Helper::roundValue($currencyConversionVAT['localAmount']);
                            $item['VATAmountRpt'] = Helper::roundValue($currencyConversionVAT['reportingAmount']);
                        }
                    }

                    $item['netAmount'] =   ($item['unitCost'] - $item['discountAmount'] + $item['VATAmount']) * $item['noQty'];

                    $currencyConversionDefaultMaster = Helper::currencyConversion($grvMaster['companySystemID'], $grvMaster['supplierTransactionCurrencyID'], $supplierCurrency->currencyID, 0);
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

                    $item['supplierItemCurrencyID'] = $grvMaster['supplierTransactionCurrencyID'];
                    $item['foreignToLocalER'] = $grvMaster['supplierTransactionER'];

                    $item['supplierDefaultCurrencyID'] = $grvMaster['supplierDefaultCurrencyID'];
                    $item['supplierDefaultER'] = $grvMaster['supplierDefaultER'];

                    $item['companyReportingER'] = $grvMaster['companyReportingER'];
                    $item['localCurrencyER'] = $grvMaster['localCurrencyER'];
                    $item['localCurrencyER'] = $grvMaster['localCurrencyER'];
                    $poDate = now();
                    $item['budgetYear'] = CompanyFinanceYear::budgetYearByDate($poDate, $grvMaster['companySystemID']);
                    $company = Company::where('companySystemID', $grvMaster['companySystemID'])->first();

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
                    $uploadedItemUomKeys[$itemUomKey] = true;
                }
                $successCount += 1;
            }
            $rowNumber++;
            $excelRowCount++;
        }

        $data = [
            'itemDetails' => $validatedItemsArray,
            'errorLog' => $validationErrorMsg,
            'successCount' => $successCount,
            'excelRowCount' => $excelRowCount
        ];
        return $data;
    }

    private static function buildRowColumnReasonError($row, $column, $reason)
    {
        return 'Row ' . $row . ': ' . $column . ' - ' . $reason;
    }

    private static function countDecimalPlaces($value)
    {
        $value = trim((string) $value);
        if ($value === '' || stripos($value, 'e') !== false) {
            return 0;
        }
        $parts = explode('.', $value, 2);
        return isset($parts[1]) ? strlen(rtrim($parts[1], '0')) : 0;
    }

    public static function errorLogUpdate($errorData, $grvAutoID)
    {
        foreach ($errorData as $details) {
            $insertError = [
                'grvAutoID' => $grvAutoID,
                'error' => $details
            ];
            GRVBulkUploadErrorLog::create($insertError);
        }
    }
}
