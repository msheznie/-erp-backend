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
use App\helper\DeliveryOrderAddMutipleItemsService;
use App\helper\CommonJobService;


class AddMultipleItemsToDeliveryOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $record;
    public $deliveryOrder;
    public $timeout = 500;
    public $db;
    public $authID;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record,$deliveryOrder,$db,$authID)
    {

        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->record = $record;
        $this->deliveryOrder = $deliveryOrder;
        $this->db = $db;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->db;
        CommonJobService::db_switch($db);
        DeliveryOrderAddMutipleItemsService::addMultipleItems($this->record,$this->deliveryOrder, $this->db,$this->authID);
    }


   
}
