<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemAssigned;
use App\Models\AssetFinanceCategory;
use App\Models\GRVDetails;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\CompanyPolicyMaster;
use App\Models\PurchaseRequestDetails;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;
use App\helper\PurcahseRequestDetail;
use App\Http\Controllers\AppBaseController;
use App\helper\CommonJobService;
class PrBulkBulkItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $data;
    public $dispatch_db;
    public $timeout = 500;
    public function __construct($input,$dispatch_db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        
        $this->data = $input;
        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        Log::useFiles(storage_path() . '/logs/po_bulk_item.log');
        Log::info('---- Job  Start-----' . date('H:i:s'));
        CommonJobService::db_switch($db);

        $input = $this->data;
        DB::beginTransaction();
        try {
        $base_controller = app()->make(AppBaseController::class);
        $input = $base_controller->convertArrayToSelectedValue($input, ['financeCategoryMaster', 'financeCategorySub']);

        $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
                                          ->first();

        if (!$purchaseRequest) {
            Log::error('PR not found');
            return;
        }
        $budgetYear = $purchaseRequest->budgetYear;
        $companyID = $purchaseRequest->companyID;
            
        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                                                    ->where('companySystemID', $purchaseRequest->companySystemID)
                                                    ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;
            if ($policy == 0) {
                if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {
                    return ['status' => false , 'message' => 'Category is not found.'];
                }
                $pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
                    ->first();
                if ($pRDetailExistSameItem) {
                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                        return ['status' => false , 'message' => 'You cannot add different category item'];
                    }
                }
            }
        }
           
        $companyId = $input['companySystemID'];
        $itemMasters = ItemMaster::whereHas('itemAssigned', function ($query) use ($companyId) {
                                    return $query->where('companySystemID', '=', $companyId)->where('isAssigned', -1);
                                 })->where('isActive',1)
                                 ->where('itemApprovedYN',1)
                                 ->when((isset($input['financeCategoryMaster']) && $input['financeCategoryMaster']), function($query) use ($input){
                                    $query->where('financeCategoryMaster', $input['financeCategoryMaster']);
                                 })
                                 ->when((isset($input['financeCategorySub']) && $input['financeCategorySub']), function($query) use ($input){
                                    $query->where('financeCategorySub', $input['financeCategorySub']);
                                 })
                                 ->whereDoesntHave('purchase_request_details', function($query) use ($input) {
                                    $query->where('purchaseRequestID', $input['purchaseRequestID']);
                                 })
                                 ->with(['unit', 'unit_by', 'financeMainCategory', 'financeSubCategory'])
                                 ->get();
        
        $validationFailedItems = [];
        $totalItemCount = count($itemMasters);

        foreach($itemMasters as $item) {
            $data = [
                "companySystemID" => $input['companySystemID'],
                "purcahseRequestID" => $input['purchaseRequestID'],
                "itemCodeSystem" => $item->itemCodeSystem
            ];
            $add = app()->make(PurcahseRequestDetail::class);
            $purchaseRequestDetailsValidation = $add->validateItemOnly($data);
            if(!$purchaseRequestDetailsValidation['status']) {
                array_push($validationFailedItems,$item);
            }
        }
    
    
        $addedItems = $totalItemCount - count($validationFailedItems);
    
      
        $itemsToAdd = $itemMasters->diff(collect($validationFailedItems));
        $dataToAdd = [];
        foreach($itemsToAdd as $itemToAdd) {
            $name = $itemToAdd->barcode.'|'.$itemToAdd->itemDescription;
            $itemToAdd['purchaseRequestID'] =  $input['purchaseRequestID'];
            $itemToAdd['companySystemID'] =  $input['companySystemID'];
            $itemToAdd['partNumber'] =  "-";
            $itemToAdd['isMRPulled'] =  false;

            $item = ItemAssigned::where('itemCodeSystem', $itemToAdd->itemCodeSystem)
                                ->where('companySystemID', $input['companySystemID'])
                                ->first();

            if ($item) {
                $currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);

                $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
                    ->where('mainItemCategoryID', $item->financeCategoryMaster)
                    ->where('itemCategorySubID', $item->financeCategorySub)
                    ->first();

                if ($financeItemCategorySubAssigned) {
                    $financeGLcodePLSystemID = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
                    $financeGLcodePL = $financeItemCategorySubAssigned->financeGLcodePL;
                    if ($item->financeCategoryMaster == 3) {
                        $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
                        if ($assetCategory) {
                            $financeGLcodePLSystemID = $assetCategory->COSTGLCODESystemID;
                            $financeGLcodePL = $assetCategory->COSTGLCODE;
                        } 
                    } 

                    $group_companies = \Helper::getSimilarGroupCompanies($input['companySystemID']);
                    $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
                                                $query->whereIn('companySystemID', $group_companies)
                                                    ->where('approved', -1)
                                                    ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                                                    ->where('poCancelledYN', 0)
                                                    ->where('manuallyClosed', 0);
                                                 })
                                                ->where('itemCode', $itemToAdd->itemCodeSystem)
                                                ->where('manuallyClosed',0)
                                                ->groupBy('erp_purchaseorderdetails.itemCode')
                                                ->select(
                                                    [
                                                        'erp_purchaseorderdetails.companySystemID',
                                                        'erp_purchaseorderdetails.itemCode',
                                                        'erp_purchaseorderdetails.itemPrimaryCode'
                                                    ]
                                                )
                                                ->sum('noQty');

                    $quantityInHand = ErpItemLedger::where('itemSystemCode', $itemToAdd->itemCodeSystem)
                                                ->where('companySystemID', $input['companySystemID'])
                                                ->groupBy('itemSystemCode')
                                                ->sum('inOutQty');


                    $grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
                                        $query->whereIn('companySystemID', $group_companies)
                                            ->where('grvTypeID', 2)
                                            ->where('approved', -1)
                                            ->groupBy('erp_grvmaster.companySystemID');
                                    })->whereHas('po_detail', function ($query){
                                        $query->where('manuallyClosed',0)
                                        ->whereHas('order', function ($query){
                                            $query->where('manuallyClosed',0);
                                        });
                                    })
                                        ->where('itemCode', $itemToAdd->itemCodeSystem)
                                        ->groupBy('erp_grvdetails.itemCode')
                                        ->select(
                                            [
                                                'erp_grvdetails.companySystemID',
                                                'erp_grvdetails.itemCode'
                                            ])
                                        ->sum('noQty');

                    $quantityOnOrder = $poQty - $grvQty;


                    $data = ([
                        "budgetYear" => $budgetYear,
                        "companyID" => $companyID,
                        "companySystemID" => $input['companySystemID'],
                        "estimatedCost" => $currencyConversion['documentAmount'],
                        "financeGLcodePL" => $financeGLcodePL,
                        "financeGLcodePLSystemID" => $financeGLcodePLSystemID,
                        "financeGLcodebBS" => $financeItemCategorySubAssigned->financeGLcodebBS,
                        "financeGLcodebBSSystemID" => $financeItemCategorySubAssigned->financeGLcodebBSSystemID,
                        "includePLForGRVYN" => $financeItemCategorySubAssigned->includePLForGRVYN,
                        "itemCategoryID" => 0,
                        "itemCode" => $itemToAdd->itemCodeSystem,
                        "itemDescription" => $itemToAdd->itemDescription,
                        'itemFinanceCategoryID' => $item->financeCategoryMaster,
                        'itemFinanceCategorySubID' => $item->financeCategorySub,
                        "itemPrimaryCode" => $itemToAdd->primaryCode,
                        'maxQty' => $item->maximunQty,
                        'minQty' => $item->minimumQty,
                        "partNumber" => $itemToAdd->secondaryItemCode,
                        'poQuantity' => $poQty,
                        "purchaseRequestID" => $input['purchaseRequestID'],
                        "quantityInHand" =>  $quantityInHand,
                        "quantityOnOrder" =>  $quantityOnOrder,
                        "isMRPulled" => false,
                        "unitOfMeasure" => $itemToAdd->unit,
                    ]);
                    array_push($dataToAdd,$data);
                }
            }

        }
        $purchaseRequest->isBulkItemJobRun = 0;
        $purchaseRequest->update();
        $purchaseRequestDetails = PurchaseRequestDetails::insert($dataToAdd);
        Log::info('succefully added PR items');
        DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Error');
            Log::error($exception->getMessage());
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
