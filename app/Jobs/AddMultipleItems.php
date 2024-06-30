<?php

namespace App\Jobs;


use App\helper\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseOrderDetails;
use App\Models\ItemMaster;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\helper\ProcumentOrderService;


class AddMultipleItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $purchaseOrder;
    public $timeout = 500;
    public $db;
    public $authID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record,$purchaseOrder,$db,$authID)
    {

        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->record = $record;
        $this->purchaseOrder = $purchaseOrder;
        $this->db = $db;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ProcumentOrderService::addMultipleItems($this->record,$this->purchaseOrder, $this->db,$this->authID);
    }


   
}
