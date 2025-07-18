<?php

namespace App\Repositories;

use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use Illuminate\Contracts\Foundation\Application;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CircularAmendmentsEditLogRepository
 * @package App\Repositories
 * @version April 11, 2023, 1:34 pm +04
 *
 * @method CircularAmendmentsEditLog findWithoutFail($id, $columns = ['*'])
 * @method CircularAmendmentsEditLog find($id, $columns = ['*'])
 * @method CircularAmendmentsEditLog first($columns = ['*'])
*/
class CircularAmendmentsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'amendment_id',
        'circular_id',
        'master_id',
        'modify_type',
        'ref_log_id',
        'status',
        'tender_id',
        'vesion_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CircularAmendmentsEditLog::class;
    }

    public function saveInitialRecord($tender_id){
        try{
            $circularData = CircularAmendments::getCircularAmendmentForAmd($tender_id);
            if(!empty($circularData)){
                foreach($circularData as $record){
                    $level_no = $this->model->getLevelNo($record['id'], $tender_id);
                    $recordData = $record->toArray();
                    $recordData['id'] = $record['id'];
                    $recordData['level_no'] = $level_no;
                    $recordData['vesion_id'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function save($tender_id, $version_id){
        try{
            $circularData = CircularAmendments::getCircularAmendmentForAmd($tender_id);
            if(!empty($circularData)){
                foreach($circularData as $record){
                    $level_no = $this->model->getLevelNo($record['id'], $tender_id);
                    $recordData = $record->toArray();
                    $recordData['id'] = $record['id'];
                    $recordData['level_no'] = $level_no;
                    $recordData['vesion_id'] = $version_id;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
