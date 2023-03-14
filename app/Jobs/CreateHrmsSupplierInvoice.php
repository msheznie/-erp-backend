<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\DocumentApproved;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class CreateHrmsSupplierInvoice implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $dataBase;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataBase,$masterModel)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->dataBase = $dataBase;

        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {

        CommonJobService::db_switch($this->dataBase);
        DB::beginTransaction();
        try {
            Log::useFiles(storage_path() . '/logs/hrms_create_supplier_invoice.log');
            $params = array('autoID' => $this->masterModel->bookingSuppMasInvAutoID,
                'company' => $this->masterModel->companySystemID,
                'document' => $this->masterModel->documentSystemID,
                'segment' => '',
                'category' => '',
                'amount' => ''
            );


            $confirm = \Helper::confirmDocumentForApi($params);
            Log::info($confirm);


            $documentApproveds = DocumentApproved::where('documentSystemCode', $this->masterModel->bookingSuppMasInvAutoID)->where('documentSystemID', 11)->get();

            foreach ($documentApproveds as $documentApproved) {
                $documentApproved["approvedComments"] = "Generated Supplier Invoice through HRMS system";
                $documentApproved["db"] = $this->dataBase;
               $approved = \Helper::approveDocumentForApi($documentApproved);
                Log::info($approved);

            }

            DB::commit();

        }
        catch (\Exception $e)
        {
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error Line No: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- GL  End with Error-----' . date('H:i:s'));
        }

    }
}
