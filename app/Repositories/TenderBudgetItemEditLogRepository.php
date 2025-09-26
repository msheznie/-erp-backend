<?php

namespace App\Repositories;

use App\Models\SrmTenderBudgetItem;
use App\Models\TenderBudgetItemEditLog;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBudgetItemEditLogRepository
 * @package App\Repositories
 * @version June 11, 2025, 12:21 pm +04
 *
 * @method TenderBudgetItemEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderBudgetItemEditLog find($id, $columns = ['*'])
 * @method TenderBudgetItemEditLog first($columns = ['*'])
*/
class TenderBudgetItemEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'budget_amount',
        'id',
        'item_id',
        'level_no',
        'tender_id',
        'version_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBudgetItemEditLog::class;
    }

    public function saveTenderBudgetItemHistory($tenderID, $version_id=null){
        try{
            return DB::transaction(function () use ($tenderID, $version_id) {
                $tenderBudgetData = SrmTenderBudgetItem::getTenderBudgetItemForAmd($tenderID);
                if(!empty($tenderBudgetData)){
                    foreach($tenderBudgetData as $record){
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
