<?php

namespace App\Repositories;

use App\Models\CircularAmendments;
use App\Models\CircularAmendmentsEditLog;
use App\Models\CircularSuppliers;
use App\Models\DocumentAttachmentsEditLog;
use App\Models\TenderCirculars;
use App\Models\TenderCircularsEditLog;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderCircularsEditLogRepository
 * @package App\Repositories
 * @version April 11, 2023, 11:53 am +04
 *
 * @method TenderCircularsEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderCircularsEditLog find($id, $columns = ['*'])
 * @method TenderCircularsEditLog first($columns = ['*'])
 */
class TenderCircularsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'attachment_id',
        'circular_name',
        'company_id',
        'description',
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
        return TenderCircularsEditLog::class;
    }
    public function saveTenderCircularForAmd($tenderID, $versionID=null){
        try{
            return DB::transaction(function () use ($tenderID, $versionID) {
                $tenderCircularData = TenderCirculars::getTenderCircularForAmd($tenderID);
                if(!empty($tenderCircularData)){
                    foreach($tenderCircularData as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['vesion_id'] = $versionID;
                        $circular = TenderCircularsEditLog::create($recordData);
                        self::saveTenderCircularAmendment($tenderID, $circular->amd_id, $versionID);
                    }
                }
                return ['success' => false, 'message' => 'Success'];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public static function saveTenderCircularAmendment($tenderID, $circularID, $versionID=null){
        try{
            return DB::transaction(function () use ($tenderID, $circularID, $versionID) {
                $tenderCircularAmendment = CircularAmendments::getCircularAmendmentForAmd($tenderID);
                if(!empty($tenderCircularAmendment)){
                    foreach($tenderCircularAmendment as $record){
                        $levelNo = CircularAmendmentsEditLog::getLevelNo($record['id']);
                        $amdAttachment = DocumentAttachmentsEditLog::getLatestAttachmentAmdID($record->amendment_id);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['circular_id'] = $circularID;
                        $recordData['amendment_id'] = $amdAttachment['amd_id'];
                        $recordData['id'] = $record['id'];
                        $recordData['vesion_id'] = $versionID;
                        CircularAmendmentsEditLog::create($recordData);
                    }
                }
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
