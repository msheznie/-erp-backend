<?php

namespace App\Repositories;

use App\Models\TenderBoqItems;
use App\Models\TenderBoqItemsEditLog;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderBoqItemsEditLogRepository
 * @package App\Repositories
 * @version April 7, 2023, 1:35 pm +04
 *
 * @method TenderBoqItemsEditLog findWithoutFail($id, $columns = ['*'])
 * @method TenderBoqItemsEditLog find($id, $columns = ['*'])
 * @method TenderBoqItemsEditLog first($columns = ['*'])
*/
class TenderBoqItemsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'description',
        'item_name',
        'main_work_id',
        'master_id',
        'modify_type',
        'qty',
        'tender_edit_version_id',
        'tender_id',
        'tender_ranking_line_item',
        'uom'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBoqItemsEditLog::class;
    }

    public function saveInitialRecord($tenderID){
        try{
            $boqItemsData = TenderBoqItems::getTenderBoqItemsAmd($tenderID);
            if(!empty($boqItemsData)){
                foreach($boqItemsData as $record){
                    $levelNo = $this->model->getLevelNo($record['id']);
                    $recordData = $record->toArray();
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['id'];
                    $recordData['tender_edit_version_id'] = null;
                    $recordData['modify_type'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function save($tenderID, $version_id){
        try{
            $boqItemsData = TenderBoqItems::getTenderBoqItemsAmd($tenderID);
            if(!empty($boqItemsData)){
                foreach($boqItemsData as $record){
                    $levelNo = $this->model->getLevelNo($record['id']);
                    $recordData = $record->toArray();
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['id'];
                    $recordData['tender_edit_version_id'] = $version_id;
                    $recordData['modify_type'] = null;
                    $this->model->create($recordData);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

}
