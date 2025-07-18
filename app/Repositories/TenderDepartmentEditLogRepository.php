<?php

namespace App\Repositories;

use App\Models\TenderDepartmentEditLog;
use App\Models\SrmTenderDepartment;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderDepartmentEditLogRepository
 * @package App\Repositories
 * @version June 11, 2025, 12:19 pm +04
 *
 * @method TenderDepartmentEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderDepartmentEditLog find($id, $columns = ['*'])
 * @method TenderDepartmentEditLog first($columns = ['*'])
*/
class TenderDepartmentEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'department_id',
        'id',
        'level_no',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderDepartmentEditLog::class;
    }
    public function saveTenderDepartmentHistory($tenderID, $version_id=null){
        try{
            return DB::transaction(function () use ($tenderID, $version_id) {
                $tenderDepartmentData = SrmTenderDepartment::getTenderDepartmentEditLog($tenderID);
                if(!empty($tenderDepartmentData)){
                    foreach($tenderDepartmentData as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['version_id'] = $version_id;
                        $this->model->create($recordData);
                    }
                }
                return ['success' => false, 'message' => 'Success'];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
