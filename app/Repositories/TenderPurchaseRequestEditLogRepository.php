<?php

namespace App\Repositories;

use App\Models\TenderPurchaseRequestEditLog;
use App\Models\TenderPurchaseRequest;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderPurchaseRequestEditLogRepository
 * @package App\Repositories
 * @version June 11, 2025, 12:22 pm +04
 *
 * @method TenderPurchaseRequestEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderPurchaseRequestEditLog find($id, $columns = ['*'])
 * @method TenderPurchaseRequestEditLog first($columns = ['*'])
*/
class TenderPurchaseRequestEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'id',
        'level_no',
        'purchase_request_id',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderPurchaseRequestEditLog::class;
    }

    public function saveTenderPurchaseRequestHistory($tenderID, $version_id=null){
        try{
            return DB::transaction(function () use ($tenderID, $version_id) {
                $purchaseRequestData = TenderPurchaseRequest::getTenderPurchaseRequestForAmd($tenderID);
                if(!empty($purchaseRequestData)){
                    foreach($purchaseRequestData as $record){
                        $levelNo = $this->model->getLevelNo($record['id']);
                        $recordData = $record->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $record['id'];
                        $recordData['version_id'] = $version_id;
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
