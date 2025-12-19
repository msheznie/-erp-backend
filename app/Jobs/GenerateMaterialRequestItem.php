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
use App\Models\MaterielRequestDetails;
use App\Models\MaterielRequest;
use App\Models\ItemMaster;
use Illuminate\Support\Facades\DB;
use App\helper\PurcahseRequestDetail;
use App\Http\Controllers\AppBaseController;
use App\helper\CommonJobService;

class GenerateMaterialRequestItem implements ShouldQueue
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
            CommonJobService::db_switch($db);


            $input = $this->data;
            $base_controller = app()->make(AppBaseController::class);
            $input = $base_controller->convertArrayToSelectedValue($input, ['MaterialRequestID', 'purchaseRequestID']);
          
            $purchaseRequestID = $input['purchaseRequestID'];
            $MaterialRequestID = $input['MaterialRequestID'];

            $mrEligibledPrDetails = PurchaseRequestDetails::where('purchaseRequestID',$purchaseRequestID)->where('is_eligible_mr', 1)->get();
            foreach($mrEligibledPrDetails as $prDetails){
                
                $prData = [ 'RequestID'=>$MaterialRequestID,
                            'comments'=>$prDetails->comments,
                            'companySystemID'=>$prDetails->companySystemID,
                            'itemCategoryID'=>$prDetails->itemCategoryID,
                            'itemCode'=>$prDetails->itemCode,
                            'itemDescription'=>$prDetails->itemDescription,
                            'maxQty'=>$prDetails->maxQty,
                            'minQty'=>$prDetails->minQty,
                            'partNumber'=>$prDetails->partNumber,
                            'quantityInHand'=>$prDetails->quantityInHand,
                            'quantityOnOrder'=>$prDetails->quantityOnOrder,
                            'quantityRequested'=>$prDetails->quantityRequested,
                            'totalCost'=>$prDetails->totalCost,
                            'unitOfMeasure'=>$prDetails->unitOfMeasure,];
                $createMrDetails = MaterielRequestDetails::create($prData);
                if($createMrDetails){
                    $prDetails->delete();
                }
            }

            $PrDetails = PurchaseRequestDetails::where('purchaseRequestID',$purchaseRequestID)->get();
            if(count($PrDetails) == 0 ){
                $PrInput['purchaseRequestId'] = $purchaseRequestID;
                $PrInput['isTrusted'] = true;
                $PrInput['reopenComments'] = "Reopened From Auto MR";
                $add = app()->make(PurcahseRequestDetail::class);
                $purchaseRequestReopen = $add->purchaseRequestReopen($PrInput);
            }
            
            $isJobData = ['is_job_run'=>0];
            $isJobUpdate = MaterielRequest::where('RequestID', $MaterialRequestID)->update($isJobData);

        
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
