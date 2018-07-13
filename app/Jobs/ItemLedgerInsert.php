<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $masterModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel)
    {
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    /**
     * A common function to inster entry to item ledger
     * @param $params : accept parameters as an object
     * $param 1-documentSystemID : document id
     * no return values
     */
    public function handle()
    {

        $masterModel = $this->masterModel;

        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                return ['success' => false, 'message' => 'Parameter document id is missing'];
            }
            //DB::beginTransaction();
            try {

            $docInforArr = array('confirmColumnName' => '',
                                  'approvedColumnName' => '',
                                  'modelName' => '',
                                  'childRelation' => '',
                                  'autoID' => ''
                                 );

                switch ($masterModel["documentSystemID"]) { // check the document id and set relevant parameters
                    case 3:
                        $docInforArr["confirmColumnName"]      = 'grvConfirmedYN';
                        $docInforArr["approvedColumnName"]     = 'approved';
                        $docInforArr["modelName"]              = 'GRVMaster';
                        $docInforArr["childRelation"]          = 'details';
                        $docInforArr["autoID"]                 = 'grvAutoID';

                        break;
                    default:
                        return ['success' => false, 'message' => 'Document ID not found'];
                }

                $nameSpacedModel =  'App\Models\\' . $docInforArr["modelName"]; // Model name
                $masterRec       = $nameSpacedModel::with([$docInforArr["childRelation"]])->find($masterModel[$docInforArr["autoID"]]);
                if($masterRec) {
                   foreach ($masterRec[$docInforArr["childRelation"]] as $detail)
                   {
                        Log::info($detail);
                    }
                }

                Log::info('location: item ledger Add' . date('H:i:s'));

            } catch (\Exception $e) {
               // DB::rollback();
                return ['success' => false, 'message' => $e . 'Error Occurred'];
            }
        } else {
            Log::info('location: Not exist' . date('H:i:s'));
            return ['success' => false, 'message' => 'Error'];
        }

    }
}
