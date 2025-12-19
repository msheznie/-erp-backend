<?php

namespace App\Jobs;

use App\Models\GeneralLedger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RollBackApproval implements ShouldQueue
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
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/rollback_approval_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                if (in_array($masterModel["documentSystemID"], [3, 8, 12, 13, 10, 20, 61, 24, 7, 19, 15, 11, 4, 21, 22, 17, 23, 41])) { // already GL entry passed Check
                    $outputGL = GeneralLedger::where('documentSystemCode',$masterModel["documentSystemCode"])->where('documentSystemID',$masterModel["documentSystemID"])->first();
                    if($outputGL){
                        $deleteOutputGL = GeneralLedger::where('documentSystemCode',$masterModel["documentSystemCode"])->where('documentSystemID',$masterModel["documentSystemID"])->delete();
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
