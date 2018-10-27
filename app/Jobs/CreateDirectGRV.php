<?php

namespace App\Jobs;

use App\Repositories\CustomerInvoiceDirectDetailRepository;
use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CreateDirectGRV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $disposalMaster;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($disposalMaster)
    {
        //
        $this->disposalMaster = $disposalMaster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerInvoiceDirectRepository $customerInvoiceRep,
                           CustomerInvoiceDirectDetailRepository $customerInvoiceDetailRep)
    {
        //
        Log::useFiles(storage_path() . '/logs/create_direct_grv_jobs.log');
        $dpMaster = $this->disposalMaster;

    }
}
