<?php

namespace App\Repositories;

use App\Models\PricingScheduleDetailEditLog;
use Illuminate\Contracts\Foundation\Application;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\PricingScheduleDetailRepository;

/**
 * Class PricingScheduleDetailEditLogRepository
 * @package App\Repositories
 * @version April 5, 2023, 8:58 am +04
 *
 * @method PricingScheduleDetailEditLog findWithoutFail($id, $columns = ['*'])
 * @method PricingScheduleDetailEditLog find($id, $columns = ['*'])
 * @method PricingScheduleDetailEditLog first($columns = ['*'])
*/
class PricingScheduleDetailEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_detail_id',
        'bid_format_id',
        'boq_applicable',
        'company_id',
        'created_by',
        'deleted_by',
        'description',
        'field_type',
        'formula_string',
        'is_disabled',
        'label',
        'modify_type',
        'pricing_schedule_master_id',
        'tender_edit_version_id',
        'tender_id',
        'tender_ranking_line_item',
        'updated_by'
    ];

    protected  $pricingScheduleDetailRepository;
    public function __construct(PricingScheduleDetailRepository $pricingScheduleDetailRepository, Application $app)
    {
        parent::__construct($app);
        $this->pricingScheduleDetailRepository = $pricingScheduleDetailRepository;
    }
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PricingScheduleDetailEditLog::class;
    }
    public function saveInitialRecord($tenderID){
        try{
            $scheduleDetailData = $this->pricingScheduleDetailRepository->getPricingScheduleDetailForAmd($tenderID);
            if(!empty($scheduleDetailData)){
                foreach($scheduleDetailData as $record){
                    $levelNo = $this->model->getLevelNo($record['id']);
                    $recordData = $record->toArray();
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['id'];
                    $recordData['tender_edit_version_id'] = null;
                    $recordData['modify_type'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function save($tenderID, $version_id){
        try{
            $scheduleDetailData = $this->pricingScheduleDetailRepository->getPricingScheduleDetailForAmd($tenderID);
            if(!empty($scheduleDetailData)){
                foreach($scheduleDetailData as $record){
                    $levelNo = $this->model->getLevelNo($record['id']);
                    $recordData = $record->toArray();
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['id'];
                    $recordData['tender_edit_version_id'] = $version_id;
                    $recordData['modify_type'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
