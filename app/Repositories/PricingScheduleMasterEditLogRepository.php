<?php

namespace App\Repositories;

use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\PricingScheduleMaster;
use App\Models\PricingScheduleMasterEditLog;
use App\Models\ScheduleBidFormatDetailsLog;
use App\Models\ScheduleBidFormatDetails;
use App\Models\TenderBoqItemsEditLog;
use App\Models\TenderBoqItems;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PricingScheduleMasterEditLogRepository
 * @package App\Repositories
 * @version April 5, 2023, 8:56 am +04
 *
 * @method PricingScheduleMasterEditLog findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleMasterEditLog find($id, $columns = ['*'])
 * @method PricingScheduleMasterEditLog first($columns = ['*'])
*/
class PricingScheduleMasterEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'items_mandatory',
        'modify_type',
        'price_bid_format_id',
        'schedule_mandatory',
        'scheduler_name',
        'status',
        'tender_edit_version_id',
        'tender_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleMasterEditLog::class;
    }

    public function saveTenderPricingScheduleMasters($tenderMasterID, $versionID = null){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID) {
                $pricingScheduleMaster = PricingScheduleMaster::getPricingScheduleMasterForAmd($tenderMasterID);
                if($pricingScheduleMaster){
                    foreach($pricingScheduleMaster as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['tender_edit_version_id'] = $versionID;
                        $pricingMaster = $this->model->create($recordData);
                        self::saveTenderPricingScheduleDetails($tenderMasterID, $versionID, $pricingMaster);
                    }
                }
                return ['success' => false, 'message' => 'Pricing schedule master created successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public function saveTenderPricingScheduleDetails($tenderMasterID, $versionID, $pricingMaster){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $pricingMaster) {
                $pricingDetails = PricingScheduleDetail::getPricingScheduleDetailForAmd($tenderMasterID, $pricingMaster->id);
                if($pricingDetails){
                    foreach($pricingDetails as $record){
                        $levelNo = PricingScheduleDetailEditLog::getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['tender_edit_version_id'] = $versionID;
                        $recordData['amd_pricing_schedule_master_id'] = $pricingMaster->amd_id;
                        $pricingDetails = PricingScheduleDetailEditLog::create($recordData);
                        self::saveScheduleBidFormatDetailsLog($tenderMasterID, $versionID, $pricingMaster, $pricingDetails);
                        self::saveTenderBoqItems($tenderMasterID, $versionID, $pricingDetails);
                    }
                }
                return ['success' => false, 'message' => 'Pricing schedule details created successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public function saveScheduleBidFormatDetailsLog($tenderMasterID, $versionID, $pricingMaster, $pricingDetails){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $pricingMaster, $pricingDetails) {
                $scheduleBids = ScheduleBidFormatDetails::getScheduleBidFormatForAmd($pricingMaster->id, $pricingDetails->id);
                if($scheduleBids){
                    foreach($scheduleBids as $record){
                        $levelNo = ScheduleBidFormatDetailsLog::getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['tender_edit_version_id'] = $versionID;
                        $recordData['amd_bid_format_detail_id'] = $pricingDetails->amd_id;
                        $recordData['amd_pricing_schedule_master_id'] = $pricingMaster->amd_id;
                        ScheduleBidFormatDetailsLog::create($recordData);
                    }
                }
                return ['success' => false, 'message' => 'Schedule bid format detail created successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
    public function saveTenderBoqItems($tenderMasterID, $versionID, $pricingDetails){
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $pricingDetails) {
                $tenderBoq = TenderBoqItems::getTenderBoqItemsAmd($tenderMasterID, $pricingDetails->id);
                if($tenderBoq){
                    foreach($tenderBoq as $record){
                        $levelNo = TenderBoqItemsEditLog::getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['tender_edit_version_id'] = $versionID;
                        $recordData['amd_main_work_id'] = $pricingDetails->amd_id;
                        TenderBoqItemsEditLog::create($recordData);
                    }
                }
                return ['success' => false, 'message' => 'Tender boq item/s created successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => 'Unexpected Error: ' . $exception->getMessage()];
        }
    }
}
