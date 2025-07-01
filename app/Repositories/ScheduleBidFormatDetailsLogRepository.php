<?php

namespace App\Repositories;

use App\Models\ScheduleBidFormatDetailsLog;
use Illuminate\Contracts\Foundation\Application;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\ScheduleBidFormatDetailsRepository;

/**
 * Class ScheduleBidFormatDetailsLogRepository
 * @package App\Repositories
 * @version April 6, 2023, 2:00 pm +04
 *
 * @method ScheduleBidFormatDetailsLog findWithoutFail($id, $columns = ['*'])
 * @method ScheduleBidFormatDetailsLog find($id, $columns = ['*'])
 * @method ScheduleBidFormatDetailsLog first($columns = ['*'])
*/
class ScheduleBidFormatDetailsLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bid_format_detail_id',
        'bid_master_id',
        'company_id',
        'master_id',
        'modify_type',
        'red_log_id',
        'schedule_id',
        'tender_edit_version_id',
        'value'
    ];

    protected $scheduleBidFormatDetailsRepository;
    public function __construct(ScheduleBidFormatDetailsRepository $scheduleBidFormatDetailsRepo, Application $app)
    {
        parent::__construct($app);
        $this->scheduleBidFormatDetailsRepository = $scheduleBidFormatDetailsRepo;
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
        return ScheduleBidFormatDetailsLog::class;
    }

    public function saveInitialRecord($tenderID){
        try{
            $scheduleBidData = $this->scheduleBidFormatDetailsRepository->getScheduleBidFormatForAmd($tenderID);
            if(!empty($scheduleBidData)){
                foreach($scheduleBidData as $record){
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
            $scheduleBidData = $this->scheduleBidFormatDetailsRepository->getScheduleBidFormatForAmd($tenderID);
            if(!empty($scheduleBidData)){
                foreach($scheduleBidData as $record){
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
