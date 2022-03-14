<?php

namespace App\Jobs;

use App\Models\SupplierMaster;
use App\Repositories\SupplierMasterRepository;
use App\Repositories\SupplierTransactionsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class CreateSupplierTransactions implements ShouldQueue
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
    public function handle(SupplierTransactionsRepository $supplierTransactionsRepository, SupplierMasterRepository $supplierMasterRepository)
    {
        DB::beginTransaction();
                try {
                    $masterModel = $this->masterModel;

                    $supplier = SupplierMaster::where('primarySupplierCode',$masterModel['supplierPrimaryCode'])->get();
                    $supplierCodeSystem = isset($supplier[0]->supplierCodeSystem) ? $supplier[0]->supplierCodeSystem : '';
                    $today = NOW();
                    $supplierMasterRepository->update(['last_activity'=>$today],$supplierCodeSystem);

                    $supplierMaster = array();
                    $supplierMaster['documentSystemID'] = $masterModel['documentSystemID'];
                    $supplierMaster['documentID'] = $masterModel['documentID'];
                    $supplierMaster['documentSystemCode'] = $masterModel['documentSystemCode'];
                    $supplierMaster['documentCode'] = $masterModel['documentCode'];
                    $supplierMaster['documentDate'] = $masterModel['documentDate'];
                    $supplierMaster['documentNarration'] = $masterModel['documentNarration'];
                    $supplierMaster['supplierID'] = $masterModel['supplierID'];
                    $supplierMaster['supplierCode'] = $masterModel['supplierCode'];
                    $supplierMaster['supplierName'] = $masterModel['supplierName'];
                    $supplierMaster['confirmedDate'] = $masterModel['confirmedDate'];
                    $supplierMaster['confirmedBy'] = $masterModel['confirmedBy'];
                    $supplierMaster['approvedDate'] = $today;
                    $supplierMaster['lastApprovedBy'] = $masterModel['lastApprovedBy'];
                    $supplierMaster['transactionCurrency'] = $masterModel['transactionCurrency'];
                    $supplierMaster['amount'] = $masterModel['amount'];
                    $supplierTransactionsRepository->create($supplierMaster);

                    DB::commit();
                }
                catch (\Exception $e)
                {
                    DB::rollback();
                }

    }
}
