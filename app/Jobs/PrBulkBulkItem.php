<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseRequest;
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
            CommonJobService::db_switch($db);


            $input = $this->data;
            $base_controller = app()->make(AppBaseController::class);
            $input = $base_controller->convertArrayToSelectedValue($input, ['financeCategoryMaster', 'financeCategorySub']);
          
    
            $purchaseRequest = PurchaseRequest::where('purchaseRequestID', $input['purchaseRequestID'])
            ->first();
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
                                        return $query->where('companySystemID', '=', $companyId);
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
                $data = ([
                    "companySystemID" => $input['companySystemID'],
                    "purchaseRequestID" => $input['purchaseRequestID'],
                    "partNumber" =>  "-",
                    "itemCode" => $itemToAdd->itemCodeSystem,
                    "itemPrimaryCode" => $itemToAdd->primaryCode,
                    "itemDescription" => $itemToAdd->itemDescription,
                    "isMRPulled" => false,
                    "unitOfMeasure" => $itemToAdd->unit,
                    "partNumber" => $itemToAdd->secondaryItemCode
                ]);
                array_push($dataToAdd,$data);
            }
            $purchaseRequest->isBulkItemJobRun = 0;
            $purchaseRequest->update();
            $purchaseRequestDetails = PurchaseRequestDetails::insert($dataToAdd);
            Log::info('succefully added PR items');

        
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
