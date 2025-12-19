<?php

namespace App\Repositories;

use App\Models\TenderSupplierAssignee;
use App\Models\TenderSupplierAssigneeEditLog;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use mysql_xdevapi\Exception;

/**
 * Class TenderSupplierAssigneeEditLogRepository
 * @package App\Repositories
 * @version June 17, 2025, 11:34 am +04
 *
 * @method TenderSupplierAssigneeEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderSupplierAssigneeEditLog find($id, $columns = ['*'])
 * @method TenderSupplierAssigneeEditLog first($columns = ['*'])
*/
class TenderSupplierAssigneeEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'id',
        'is_deleted',
        'level_no',
        'mail_sent',
        'registration_link_id',
        'registration_number',
        'supplier_assigned_id',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'updated_by',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSupplierAssigneeEditLog::class;
    }

    public function saveTenderSupplierAssignee($tenderMasterID, $versionID = null){
        $tenderSupplierAssignee = TenderSupplierAssignee::getTenderSupplierAssignForAmd($tenderMasterID);
        try{
            return DB::transaction(function () use ($tenderMasterID, $versionID, $tenderSupplierAssignee) {
                if($tenderSupplierAssignee){
                    foreach($tenderSupplierAssignee as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['version_id'] = $versionID;
                        $this->model->create($recordData);
                    }
                }
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
