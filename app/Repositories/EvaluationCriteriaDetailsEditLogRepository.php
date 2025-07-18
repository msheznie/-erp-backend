<?php

namespace App\Repositories;

use App\Models\EvaluationCriteriaDetailsEditLog;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use App\Repositories\EvaluationCriteriaDetailsRepository;
use App\Models\EvaluationCriteriaScoreConfig;
use App\Models\EvacuationCriteriaScoreConfigLog;

/**
 * Class EvaluationCriteriaDetailsEditLogRepository
 * @package App\Repositories
 * @version April 10, 2023, 11:33 am +04
 *
 * @method EvaluationCriteriaDetailsEditLog findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaDetailsEditLog find($id, $columns = ['*'])
 * @method EvaluationCriteriaDetailsEditLog first($columns = ['*'])
 */
class EvaluationCriteriaDetailsEditLogRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'answer_type_id',
        'critera_type_id',
        'description',
        'is_final_level',
        'level',
        'master_id',
        'max_value',
        'min_value',
        'modify_type',
        'parent_id',
        'passing_weightage',
        'ref_log_id',
        'sort_order',
        'tender_id',
        'weightage'
    ];
    protected  $evaluationCriteriaDetailsRepository;
    public function __construct(EvaluationCriteriaDetailsRepository $evaluationCriteriaDetailsRepo, Application $app)
    {
        parent::__construct($app);
        $this->evaluationCriteriaDetailsRepository = $evaluationCriteriaDetailsRepo;
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
        return EvaluationCriteriaDetailsEditLog::class;
    }

    public function saveEvacuationCriteriaDetails($tenderID, $version_id=null){
        try{
            $criteriaData = $this->evaluationCriteriaDetailsRepository->getEvaluationCriteriaDetailForAmd($tenderID);
            if(!empty($criteriaData)){
                foreach($criteriaData as $record){
                    $levelNo = $this->model->getLevelNo($record['id']);
                    $recordData = $record->toArray();
                    if($record->parent_id){
                        $parentDetail = EvaluationCriteriaDetailsEditLog::getAmdID($record->parent_id);
                        $recordData['parent_id'] = $parentDetail['amd_id'];
                    }
                    $recordData['level_no'] = $levelNo;
                    $recordData['id'] = $record['id'];
                    $recordData['tender_version_id'] = $version_id;
                    $recordData['modify_type'] = null;
                    $masterData = $this->model->create($recordData);
                    self::saveEvacuationCriteriaScoreConfig($version_id, $record['id'], $masterData['amd_id']);
                }
            }
            return ['success' => false, 'message' => 'Success'];
        } catch (\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function saveEvacuationCriteriaScoreConfig($versionID, $criteriaID, $amd_id)
    {
        try{
            return DB::transaction(function () use ($versionID, $criteriaID, $amd_id) {
                $evaScore = EvaluationCriteriaScoreConfig::getEvalScoreForAmend($criteriaID);
                if(!empty($evaScore)){
                    foreach($evaScore as $scoreData)
                    {
                        $levelNo = EvacuationCriteriaScoreConfigLog::getLevelNo($scoreData['id']);
                        $recordData = $scoreData->toArray();
                        $recordData['level_no'] = $levelNo;
                        $recordData['id'] = $scoreData['id'];
                        $recordData['criteria_detail_id'] = $amd_id;
                        $recordData['version_id'] = $versionID;
                        EvacuationCriteriaScoreConfigLog::create($recordData);
                    }
                }
                return ['success' => false, 'message' => 'Evaluation criteria score config created successfully'];
            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }
}
