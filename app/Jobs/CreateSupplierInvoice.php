<?php

namespace App\Jobs;

use App\Models\BookInvSuppMaster;
use App\Models\CompanyFinancePeriod;
use App\Models\StockReceive;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateSupplierInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $srMaster;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($srMaster)
    {
        $this->srMaster = $srMaster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/create_supplier_invoice_jobs.log');
        $sr = $this->srMaster;
        $srMaster = StockReceive::where('stockReceiveAutoID', $sr->stockReceiveAutoID)->first();
        if (!empty($srMaster)) {
            DB::beginTransaction();
            try {
                Log::info('Successfully start  supplier_invoice' . date('H:i:s'));
                if ($srMaster->interCompanyTransferYN == -1) {

                    $toCompanyFinancePeriod = CompanyFinancePeriod::where('companySystemID', $srMaster->companyToSystemID)
                                                                    ->where('departmentSystemID', 1)
                                                                    ->where('isActive', -1)
                                                                    ->where('isCurrent', -1)
                                                                    ->first();

                    $today = date('Y-m-d');

                    $bookingInvLastSerial = BookInvSuppMaster::where('companySystemID', $srMaster->companyToSystemID)
                                                             ->where('companyFinanceYearID', $toCompanyFinancePeriod->companyFinanceYearID)
                                                             ->where('serialNo', '>', 0)
                                                             ->orderBy('bookingSuppMasInvAutoID', 'desc')
                                                             ->first();


                    /*If chkSerial . RecordCount = 0 Then
                            mySerialNumber = 1
                        Else
                            chkSerial . MoveLast
                            mySerialorder = chkSerial("serialNo")
                            mySerialNumber = mySerialorder + 1
                        End If*/


                    Log::info('Successfully end  supplier_invoice' . date('H:i:s'));
                    }
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
