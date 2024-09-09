<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemCategoryTypeMaster;
use App\Models\ItemMaster;
use App\Models\ItemMasterCategoryType;
use App\Models\MaterielRequest;
use App\Models\MaterielRequestDetails;
use App\Models\MrBulkUploadErrorLog;
use App\Models\PurchaseOrderDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class mrBulkUploadItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $mrRequest;
    public $timeout = 500;
    public $db;
    public $authID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record, $mrRequest, $db, $authID)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->record = $record;
        $this->mrRequest = $mrRequest;
        $this->db = $db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->db;
        Log::useFiles(storage_path() . '/logs/mr_bulk_item.log');
        CommonJobService::db_switch($db);
        $record = $this->record;
        $mrRequest = $this->mrRequest;

        $materialRequest = MaterielRequest::find($mrRequest['RequestID']);
        $materialRequest->successDetailsCount = 0;
        $materialRequest->excelRowCount = 0;
        $materialRequest->save();

        $validateItem = self::validateItemUpload($record, $mrRequest, $this->authID);

        Log::info('Add Multiple Items End');
        $materialRequest = MaterielRequest::find($mrRequest['RequestID']);
        $materialRequest->isBulkItemJobRun = 0;
        $materialRequest->successDetailsCount = $validateItem['successCount'];
        $materialRequest->excelRowCount = $validateItem['excelRowCount'];
        $materialRequest->save();
    }

    public static function validateItemUpload($excelRows, $mrRequest, $authID)
    {
        $rowNumber = 7;
        $validationErrorMsg = [];
        $successCount = $excelRowCount = 0;
        $companyId = $mrRequest['companySystemID'];
        foreach ($excelRows as $rowData) {
            $isValidationError = 0;
            if (!array_key_exists('qty',$rowData) || is_null($rowData['qty'])) {
                $validationErrorMsg[] = 'The item Qty has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else if (!is_numeric($rowData['qty'])) {
                $validationErrorMsg[] = 'The quantity should be a numeric value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else if ($rowData['qty'] < 0) {
                $validationErrorMsg[] = 'The quantity should be a positive value for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if (!array_key_exists('item_description',$rowData) || is_null($rowData['item_description'])) {
                $validationErrorMsg[] = 'The item description has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            }

            if (!array_key_exists('item_code',$rowData) || is_null($rowData['item_code'])) {
                $validationErrorMsg[] = 'The item code has not been updated for Excel row: ' . $rowNumber;
                $isValidationError = 1;
            } else {

                $categoryType = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                    return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1);
                })->where('isActive',1)
                    ->where('itemApprovedYN',1)
                    ->where('primaryCode', trim($rowData['item_code']))
                    ->first();

                if ($categoryType) {
                    $checkTheCategoryType = ItemMasterCategoryType::whereIn('categoryTypeID', ItemCategoryTypeMaster::purchaseItems())
                        ->where('itemCodeSystem', $categoryType->itemCodeSystem)
                        ->first();

                    if (!$checkTheCategoryType) {
                        $validationErrorMsg[] = 'The inventory items added should only be of Item Type: Purchase or Purchase & Sales for Excel row: ' . $rowNumber;
                        $isValidationError = 1;
                    }
                } else {
                    $validationErrorMsg[] = 'The item code does not match with a system for Excel row: ' . $rowNumber;
                    $isValidationError = 1;
                }
            }

            if ($isValidationError == 0) {
                $orgItem = ItemMaster::with('itemAssigned')->where('primaryCode',trim($rowData['item_code']))->first();
                if ($orgItem) {
                    $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companyId)
                        ->where('mainItemCategoryID', $orgItem['financeCategoryMaster'])
                        ->where('itemCategorySubID', $orgItem['financeCategorySub'])
                        ->first();

                    $itemExist = 0;
                    if ($orgItem['financeCategoryMaster'] == 1) {
                        $alreadyAdded = MaterielRequest::where('RequestID', $mrRequest['RequestID'])
                            ->whereHas('details', function ($query) use ($orgItem) {
                                $query->where('itemCode', $orgItem['itemCodeSystem']);
                            })->first();

                        if ($alreadyAdded) {
                            $validationErrorMsg[] = 'The item in excel row: ' . $rowNumber . ' already added to the document';
                            $itemExist = 1;
                        }
                    }

                    if($itemExist == 0) {
                        $requestDetails['RequestID'] = $mrRequest['RequestID'];
                        $requestDetails['itemCode'] = $orgItem['itemCodeSystem'];
                        $requestDetails['itemDescription'] = $orgItem['itemDescription'];
                        $requestDetails['itemFinanceCategoryID'] = $orgItem['financeCategoryMaster'];
                        $requestDetails['itemFinanceCategorySubID'] = $orgItem['financeCategorySub'];
                        $requestDetails['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
                        $requestDetails['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
                        $requestDetails['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
                        $requestDetails['partNumber'] = $orgItem['secondaryItemCode'];
                        $requestDetails['unitOfMeasure'] = trim($orgItem['unit']);
                        $requestDetails['unitOfMeasureIssued'] = trim($orgItem['unit']);
                        $requestDetails['quantityRequested'] = $rowData['qty'];
                        $requestDetails['qtyIssuedDefaultMeasure'] = $rowData['qty'];
                        $requestDetails['convertionMeasureVal'] = 1;
                        $requestDetails['comments'] = $rowData['comment'] ? $rowData['comment'] : null;
                        $requestDetails['estimatedCost'] = 0;

                        $poQty = PurchaseOrderDetails::whereHas('order' , function ($query) use ($companyId,$mrRequest) {
                            $query->where('companySystemID', $companyId)
                                ->where('poLocation', $mrRequest->location)
                                ->where('approved', -1)
                                ->where('poCancelledYN', 0);
                        })
                            ->where('itemCode', $orgItem['itemCodeSystem'])
                            ->groupBy('erp_purchaseorderdetails.companySystemID',
                                'erp_purchaseorderdetails.itemCode')
                            ->select(
                                [
                                    'erp_purchaseorderdetails.companySystemID',
                                    'erp_purchaseorderdetails.itemCode',
                                    'erp_purchaseorderdetails.itemPrimaryCode'
                                ]
                            )
                            ->sum('noQty');

                        $quantityInHand = ErpItemLedger::where('itemSystemCode', $orgItem['itemCodeSystem'])
                            ->where('companySystemID', $companyId)
                            ->groupBy('itemSystemCode')
                            ->sum('inOutQty');

                        $grvQty = GRVDetails::whereHas('grv_master' , function ($query) use ($companyId,$mrRequest) {
                            $query->where('companySystemID', $companyId)
                                ->where('grvTypeID', 2)
                                ->groupBy('erp_grvmaster.companySystemID');
                        })
                            ->where('itemCode', $orgItem['itemCodeSystem'])
                            ->groupBy('erp_grvdetails.itemCode')
                            ->select(
                                [
                                    'erp_grvdetails.companySystemID',
                                    'erp_grvdetails.itemCode'
                                ])
                            ->sum('noQty');

                        $quantityOnOrder = $poQty - $grvQty;
                        $requestDetails['quantityOnOrder'] = $quantityOnOrder;
                        $requestDetails['quantityInHand']  = $quantityInHand;

                        if($orgItem['minimumQty']){
                            $requestDetails['minQty'] = $orgItem['minimumQty'];
                        }else{
                            $requestDetails['minQty'] = 0;
                        }
                        if($orgItem->maximunQty){
                            $requestDetails['maxQty'] = $orgItem['maximunQty'];
                        }else{
                            $requestDetails['maxQty'] = 0;
                        }
                        $requestDetails['selectedForIssue'] = 0;
                        $requestDetails['ClosedYN'] = 0;
                        $requestDetails['allowCreatePR'] = 0;
                        $requestDetails['selectedToCreatePR'] = 0;
                        $requestDetails['timeStamp'] = date('Y-m-d H:i:s');

                        MaterielRequestDetails::create($requestDetails);
                        $successCount++;
                    }
                }
            }
            $rowNumber++;
            $excelRowCount++;
        }

        if (!empty($validationErrorMsg)) {
            foreach ($validationErrorMsg as $details) {
                $insertError = [
                    'documentSystemID' => $mrRequest['RequestID'],
                    'error' => $details
                ];
                MrBulkUploadErrorLog::create($insertError);
            }
        }

        $data = [
            'successCount' => $successCount,
            'excelRowCount' => $excelRowCount
        ];
        return $data;
    }
}
