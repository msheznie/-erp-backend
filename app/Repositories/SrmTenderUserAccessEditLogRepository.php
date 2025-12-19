<?php

namespace App\Repositories;

use App\Models\SRMTenderUserAccess;
use App\Models\SrmTenderUserAccessEditLog;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrmTenderUserAccessEditLogRepository
 * @package App\Repositories
 * @version June 3, 2025, 8:11 pm +04
 *
 * @method SrmTenderUserAccessEditLog findWithoutFail($id, $columns = ['*'])
 * @method SrmTenderUserAccessEditLog find($id, $columns = ['*'])
 * @method SrmTenderUserAccessEditLog first($columns = ['*'])
*/
class SrmTenderUserAccessEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'version_id',
        'level_no',
        'tender_id',
        'user_id',
        'module_id',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmTenderUserAccessEditLog::class;
    }
    public function saveTenderUserAccessHistory($tenderID, $version_id=null){
        try{
            return DB::transaction(function () use ($tenderID, $version_id) {
                $tenderUserData = SRMTenderUserAccess::getTenderUserAccessForAmd($tenderID);
                if(!empty($tenderUserData)){
                    foreach($tenderUserData as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['version_id'] = $version_id;
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
