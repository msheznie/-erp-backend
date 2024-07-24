<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\MaterialRequestService;
use App\Models\ItemIssueMaster;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\helper\PurcahseRequestDetail;
use App\Models\ItemAssigned;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\AssetFinanceCategory;
use App\Models\PurchaseOrderDetails;
use App\Models\GRVDetails;
use App\Models\ErpItemLedger;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseRequest;

class PrBulkBulkItemProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $outputData;
    public $companyId;
    public $requestID;
    protected $budgetYear;
    protected $chunkDataSizeCounts;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $outputData,$companyId,$budgetYear,$chunkDataSizeCounts,$requestID)
    {
        $this->dispatch_db = $dispatch_db;
        $this->outputData = $outputData;
        $this->companyId = $companyId;
        $this->requestID = $requestID;
        $this->budgetYear = $budgetYear;
        $this->chunkDataSizeCounts = $chunkDataSizeCounts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        Log::useFiles(storage_path() . '/logs/pr_bulk_item.log');
        CommonJobService::db_switch($db);

        $companyId = $this->companyId;
        $requestID = $this->requestID;
        $budgetYear = $this->budgetYear;
        $chunkDataSizeCounts = $this->chunkDataSizeCounts;

        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::find($requestID);
            $output = collect($this->outputData);

            $validationFailedItems = [];
            $totalItemCount = count($output);
            $dataToAdd = [];

        foreach($output as $itemToAdd) {
            $data = [
                "companySystemID" => $companyId,
                "purcahseRequestID" => $requestID,
                "itemCodeSystem" => $itemToAdd['itemCodeSystem']
            ];
            $add = app()->make(PurcahseRequestDetail::class);
            $purchaseRequestDetailsValidation = $add->validateItemOnly($data);
            if(!$purchaseRequestDetailsValidation['status']) {
                array_push($validationFailedItems,$itemToAdd);
            }
            else
            {

                $name = $itemToAdd['barcode'].'|'.$itemToAdd['itemDescription'];
                $itemToAdd['purchaseRequestID'] =  $requestID;
                $itemToAdd['companySystemID'] =  $companyId;
                $itemToAdd['partNumber'] =  "-";
                $itemToAdd['isMRPulled'] =  false;
    
                $item = ItemAssigned::where('itemCodeSystem', $itemToAdd['itemCodeSystem'])
                                    ->where('companySystemID', $companyId)->where('isAssigned', '=', -1)->whereIn('categoryType', ['[{"id":1,"itemName":"Purchase"}]','[{"id":1,"itemName":"Purchase"},{"id":2,"itemName":"Sale"}]','[{"id":2,"itemName":"Sale"},{"id":1,"itemName":"Purchase"}]'])
                                    ->first();
    
                if ($item) {
                    if($item->wacValueLocalCurrencyID != 0)
                    {
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
    
                            $group_companies = \Helper::getSimilarGroupCompanies($companyId);
                            $poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
                                                        $query->whereIn('companySystemID', $group_companies)
                                                            ->where('approved', -1)
                                                            ->where('poType_N', '!=',5)// poType_N = 5 =>work order
                                                            ->where('poCancelledYN', 0)
                                                            ->where('manuallyClosed', 0);
                                                        })
                                                        ->where('itemCode', $itemToAdd['itemCodeSystem'])
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
    
                            $quantityInHand = ErpItemLedger::where('itemSystemCode', $itemToAdd['itemCodeSystem'])
                                                        ->where('companySystemID', $companyId)
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
                                                ->where('itemCode', $itemToAdd['itemCodeSystem'])
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
                                "companyID" => $companyId,
                                "companySystemID" => $companyId,
                                "estimatedCost" => $currencyConversion['documentAmount'],
                                "financeGLcodePL" => $financeGLcodePL,
                                "financeGLcodePLSystemID" => $financeGLcodePLSystemID,
                                "financeGLcodebBS" => $financeItemCategorySubAssigned->financeGLcodebBS,
                                "financeGLcodebBSSystemID" => $financeItemCategorySubAssigned->financeGLcodebBSSystemID,
                                "includePLForGRVYN" => $financeItemCategorySubAssigned->includePLForGRVYN,
                                "itemCategoryID" => 0,
                                "itemCode" => $itemToAdd['itemCodeSystem'],
                                "itemDescription" => $itemToAdd['itemDescription'],
                                'itemFinanceCategoryID' => $item->financeCategoryMaster,
                                'itemFinanceCategorySubID' => $item->financeCategorySub,
                                "itemPrimaryCode" => $itemToAdd['primaryCode'],
                                'maxQty' => $item->maximunQty,
                                'minQty' => $item->minimumQty,
                                "partNumber" => $itemToAdd['secondaryItemCode'],
                                'poQuantity' => $poQty,
                                "purchaseRequestID" => $requestID,
                                "quantityInHand" =>  $quantityInHand,
                                "quantityOnOrder" =>  $quantityOnOrder,
                                "isMRPulled" => false,
                                "unitOfMeasure" => $item->itemUnitOfMeasure,
                            ]);
                            array_push($dataToAdd,$data);
                        }
    
                    }
                }






            }//end
        }
    
    
        // $purchaseRequest->isBulkItemJobRun = 0;
        // $purchaseRequest->update();
        $purchaseRequestDetails = PurchaseRequestDetails::insert($dataToAdd);

        $purchaseRequest->increment('counter');

        $purchaseRequest->save();

        $newCounterValue = $purchaseRequest->counter;

        if ($newCounterValue == $chunkDataSizeCounts) {
 
            PurchaseRequest::where('purchaseRequestID', $requestID)->update(['isBulkItemJobRun' => 0]);            
        }


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
