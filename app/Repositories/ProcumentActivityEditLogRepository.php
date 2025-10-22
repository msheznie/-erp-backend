<?php

namespace App\Repositories;

use App\Models\ProcumentActivityEditLog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\ProcumentActivityRepository;

/**
 * Class ProcumentActivityEditLogRepository
 * @package App\Repositories
 * @version April 23, 2023, 6:59 pm +04
 *
 * @method ProcumentActivityEditLog findWithoutFail($id, $columns = ['*'])
 * @method ProcumentActivityEditLog find($id, $columns = ['*'])
 * @method ProcumentActivityEditLog first($columns = ['*'])
*/
class ProcumentActivityEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'category_id',
        'company_id',
        'version_id',
        'modify_type',
        'master_id',
        'ref_log_id'
    ];

    protected $procumentActivityRepository;
    public function __construct(ProcumentActivityRepository $procumentActivityRepo, Application $app)
    {
        parent::__construct($app);
        $this->procumentActivityRepository = $procumentActivityRepo;
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
        return ProcumentActivityEditLog::class;
    }
    public function saveProcurementActivityHistory($tenderID, $version_id=null){
        try{
            return DB::transaction(function () use ($tenderID, $version_id) {
                $procurementData = $this->procumentActivityRepository->getProcumentActivityForAmd($tenderID);
                if(!empty($procurementData)){
                    foreach($procurementData as $record){
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
