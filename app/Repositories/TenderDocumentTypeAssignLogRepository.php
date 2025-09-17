<?php

namespace App\Repositories;

use App\Models\TenderDocumentTypeAssign;
use App\Models\TenderDocumentTypeAssignLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderDocumentTypeAssignLogRepository
 * @package App\Repositories
 * @version May 17, 2023, 12:21 pm +04
 *
 * @method TenderDocumentTypeAssignLog findWithoutFail($id, $columns = ['*'])
 * @method TenderDocumentTypeAssignLog find($id, $columns = ['*'])
 * @method TenderDocumentTypeAssignLog first($columns = ['*'])
*/
class TenderDocumentTypeAssignLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_type_id',
        'master_id',
        'modify_type',
        'ref_log_id',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderDocumentTypeAssignLog::class;
    }

    public function saveTenderDocumentTypeAssign($tenderID, $versionID = null){
        try{
            $documentAssignData = TenderDocumentTypeAssign::getTenderDocumentTypeForAmd($tenderID);
            if(!empty($documentAssignData)){
                foreach($documentAssignData as $record){
                    $levelNo = $this->model->getLevelNo($record['id']);
                    $recordData = $record->toArray();
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['id'];
                    $recordData['version_id'] = $versionID;
                    $recordData['modify_type'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => trans('srm_tender_rfx.success')];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
