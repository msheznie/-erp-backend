<?php

namespace App\Jobs;

use App\helper\inventory;
use App\Models\CompanyPolicyMaster;
use App\Models\GRVMaster;
use App\Models\ItemMaster;
use App\Models\WarehouseItems;
use App\Repositories\WarehouseItemsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WarehouseItemUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $grvMasterAutoID;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($grvMasterAutoID)
    {
        $this->grvMasterAutoID = $grvMasterAutoID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(WarehouseItemsRepository $warehouseItemsRepository)
    {
        DB::beginTransaction();
        try {
            Log::useFiles(storage_path() . '/logs/warehouse_item_update_jobs.log');
            $grvMaster = GRVMaster::find($this->grvMasterAutoID);
            if (!empty($grvMaster) && $grvMaster->approved == -1) {
                $warehouseBinLocationPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 40)
                    ->where('companySystemID', $grvMaster->companySystemID)
                    ->where('isYesNO', 1)
                    ->exists();
                if ($warehouseBinLocationPolicy) {
                    $details = $grvMaster->details;
                    foreach ($details as $detail) {
                        if ($detail['itemFinanceCategoryID'] == 1) {
                            $warehouseItem = $warehouseItemsRepository->findWhere(['companySystemID' => $grvMaster->companySystemID,
                                'warehouseSystemCode' => $grvMaster->grvLocation,
                                'itemSystemCode' => $detail['itemCode']])
                                ->first();
                            if (!empty($warehouseItem)) {
                                $warehouseItem = WarehouseItems::where(['companySystemID' => $grvMaster->companySystemID,
                                    'warehouseSystemCode' => $grvMaster->grvLocation,
                                    'itemSystemCode' => $detail['itemCode']])
                                    ->update(['binNumber' => $detail['binNumber']]);
                            } else {
                                Log::error('warehouse item not found');
                                $item = ItemMaster::find($detail['itemCode']);

                                if(!empty($item)) {
                                    $data = array('itemCodeSystem' => $item->itemCodeSystem,
                                                  'companySystemID' => $grvMaster->companySystemID,
                                                  'wareHouseId' => $grvMaster->grvLocation
                                         );
                                    $inventory = inventory::itemCurrentCostAndQty($data);
                                    $warehouseItem['companySystemID'] = $grvMaster->companySystemID;
                                    $warehouseItem['companyID'] = $grvMaster->companyID;
                                    $warehouseItem['warehouseSystemCode'] = $grvMaster->grvLocation;
                                    $warehouseItem['itemSystemCode'] = $item->itemCodeSystem;
                                    $warehouseItem['itemPrimaryCode'] = $item->primaryCode;
                                    $warehouseItem['itemDescription'] = $item->itemDescription;
                                    $warehouseItem['unitOfMeasure'] = $item->unit;
                                    $warehouseItem['stockQty'] = $inventory['currentStockQty'];
                                    $warehouseItem['maximunQty'] = 0;
                                    $warehouseItem['minimumQty'] = 0;
                                    $warehouseItem['rolQuantity'] = 0;
                                    $warehouseItem['wacValueLocalCurrencyID'] = $grvMaster->localCurrencyID;
                                    $warehouseItem['wacValueLocal'] = $inventory['wacValueLocalWarehouse'];
                                    $warehouseItem['wacValueReportingCurrencyID'] = $grvMaster->companyReportingCurrencyID;
                                    $warehouseItem['wacValueReporting'] = $inventory['wacValueReportingWarehouse'];
                                    $warehouseItem['totalQty'] = $inventory['inOutQtyWarehouse'] ;
                                    $warehouseItem['totalValueLocal'] = $inventory['totalWacCostLocalWarehouse'];
                                    $warehouseItem['totalValueRpt'] = $inventory['totalWacCostRptWarehouse'] ;
                                    $warehouseItem['financeCategoryMaster'] = $item->financeCategoryMaster;
                                    $warehouseItem['financeCategorySub'] = $item->financeCategorySub;
                                    $warehouseItem['binNumber'] = $detail['binNumber'];
                                    $warehouseItem['toDelete'] = 0;
                                    $newWarehouseItem = $warehouseItemsRepository->create($warehouseItem);
                                }
                            }
                        }
                    }
                    DB::commit();
                } else {
                    DB::commit();
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}
