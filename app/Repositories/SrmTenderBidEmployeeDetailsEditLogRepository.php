<?php

namespace App\Repositories;

use App\Models\SrmTenderBidEmployeeDetails;
use App\Models\SrmTenderBidEmployeeDetailsEditLog;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrmTenderBidEmployeeDetailsEditLogRepository
 * @package App\Repositories
 * @version April 4, 2023, 1:00 pm +04
 *
 * @method SrmTenderBidEmployeeDetailsEditLog findWithoutFail($id, $columns = ['*'])
 * @method SrmTenderBidEmployeeDetailsEditLog find($id, $columns = ['*'])
 * @method SrmTenderBidEmployeeDetailsEditLog first($columns = ['*'])
*/
class SrmTenderBidEmployeeDetailsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'commercial_eval_remarks',
        'commercial_eval_status',
        'emp_id',
        'modify_type',
        'remarks',
        'status',
        'tender_award_commite_mem_comment',
        'tender_award_commite_mem_status',
        'tender_edit_version_id',
        'tender_id'
    ];

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmTenderBidEmployeeDetailsEditLog::class;
    }

    public function saveTenderBidEmployeeDetailHistory($tenderID, $version_id=null){
        try {
            return DB::transaction(function () use ($tenderID, $version_id) {
                $tenderBidEmpData = SrmTenderBidEmployeeDetails::getTenderBidEmployees($tenderID);
                if(!empty($tenderBidEmpData)){
                    foreach($tenderBidEmpData as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['tender_edit_version_id'] = $version_id;
                        $recordData['modify_type'] = null;
                        $this->model->create($recordData);
                    }
                }
                return ['success' => false, 'message' => trans('srm_tender_rfx.success')];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
