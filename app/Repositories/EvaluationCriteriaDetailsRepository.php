<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\EvacuationCriteriaScoreConfigLog;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Models\EvaluationCriteriaMaster;
use App\Models\EvaluationCriteriaMasterDetails;
use App\Models\EvaluationCriteriaScoreConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationCriteriaDetailsRepository
 * @package App\Repositories
 * @version April 22, 2022, 9:29 am +04
 *
 * @method EvaluationCriteriaDetails findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaDetails find($id, $columns = ['*'])
 * @method EvaluationCriteriaDetails first($columns = ['*'])
 */
class EvaluationCriteriaDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'parent_id',
        'description',
        'critera_type_id',
        'answer_type_id',
        'level',
        'is_final_level',
        'weightage',
        'passing_weightage',
        'sort_order',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationCriteriaDetails::class;
    }

    public function getEvaluationCriteriaDetailForAmd($tender_id){
        return $this->model->getEvaluationCriteriaDetailForAmd($tender_id);
    }
    public function getCriteriaDetails($tenderMasterID, $criteriaTypeID, $editOrAmend, $versionID){
        $editOrAmend = $versionID > 0;
        if($editOrAmend){
            return EvaluationCriteriaDetailsEditLog::getEvaluationCriteriaDetailsList($tenderMasterID, $criteriaTypeID, $versionID);
        }
        return EvaluationCriteriaDetails::getEvaluationCriteriaDetailsList($tenderMasterID, $criteriaTypeID);
    }
    public function normalizeCriteriaChildren(array &$criteriaList): void
    {
        foreach ($criteriaList as &$item) {
            // Rename 'child_amd' to 'child' if present
            if (isset($item['child_amd'])) {
                $item['child'] = $item['child_amd'];
                unset($item['child_amd']);
            }

            // Recurse if child exists
            if (isset($item['child']) && is_array($item['child'])) {
                $this->normalizeCriteriaChildren($item['child']);
            }
        }
    }
    public function createCriteriaDetail($data, $model, $versionID, $editOrAmend, $fromTender){
        try{
            return DB::transaction(function () use ($data, $model, $versionID, $editOrAmend, $fromTender) {
                if($fromTender && $editOrAmend){
                    $data['id'] = null;
                    $data['level_no'] = 1;
                    $data['tender_version_id'] = $versionID;
                    $result = EvaluationCriteriaDetailsEditLog::create($data);
                } else{
                    $result = $model::create($data);
                }

                if($result){
                    return ['success' => true, 'message' => trans('srm_masters.successfully_created'), 'data' => $result];
                }
                return ['success' => false, 'message' => trans('srm_masters.failed_to_create')];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => trans('srm_masters.unexpected_error') . $exception->getMessage()];
        }
    }
    public function createCriteriaScoreConfig($data, $editOrAmend, $versionID){
        try{
            return DB::transaction(function () use ($data, $versionID, $editOrAmend) {
                if($editOrAmend) {
                    $data['id'] = null;
                    $data['level_no'] = 1;
                    $data['version_id'] = $versionID;
                    $result = EvacuationCriteriaScoreConfigLog::create($data);
                } else {
                    $result = EvaluationCriteriaScoreConfig::create($data);
                }

            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => trans('srm_masters.unexpected_error') . $exception->getMessage()];
        }
    }
    public function getEvaluationDetailById($input) {
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;

        if(isset($input['isMasterCriteria']) && $input['isMasterCriteria'] == 1)
        {
            return EvaluationCriteriaMasterDetails::getEvaluationDetailById($input['evaluationId']);
        }
        else
        {
            return $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::getEvaluationDetailById($input['evaluationId'], $versionID) :
                EvaluationCriteriaDetails::getEvaluationDetailById($input['evaluationId']);
        }
    }
    public function getEvaluationCriteriaMaster($tenderMasterID, $versionID, $editOrAmend, $uniqueIds = []){
        return EvaluationCriteriaMaster::getEvaluationCriteriaMaster($tenderMasterID, $versionID, $editOrAmend, $uniqueIds);
    }
    public function editEvaluationCriteriaDetails($input){
        try{
            if ($input['level'] == 1 && $input['critera_type_id'] != 1) {
                if (empty($input['weightage']) || $input['weightage'] <= 0) {
                    return ['success' => false, 'message' => trans('srm_masters.weightage_is_required')];
                }

                if (empty($input['passing_weightage']) || $input['passing_weightage'] <= 0) {
                    return ['success' => false, 'message' => trans('srm_masters.passing_weightage_is_required')];
                }
            }


            if (!empty($input['is_final_level']) && empty($input['answer_type_id'])) {
                return ['success' => false, 'message' => trans('srm_masters.answer_type_is_required')];
            }

            $versionID = $input['versionID'] ?? 0;
            $editOrAmend = $versionID > 0;

            $chkDuplicate = $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::checkForDescriptionDuplication($input['tender_id'], $input['description'], $input['level'], $versionID, $input['amd_id']) :
                EvaluationCriteriaDetails::checkForDescriptionDuplication($input['tender_id'], $input['description'], $input['level'], $input['id']);

            if(!empty($chkDuplicate)){
                return ['success' => false, 'message' => trans('srm_masters.description_cannot_be_duplicated')];
            }

            return DB::transaction(function () use ($input, $editOrAmend, $versionID) {
                $employee = Helper::getEmployeeInfo();
                if($input['is_final_level'] == 1 && $input['critera_type_id'] == 2  && ($input['answer_type_id'] == 1 || $input['answer_type_id'] == 3)){
                    $data['max_value'] = $input['max_value'];
                }

                $data['description'] = $input['description'];
                if(isset($input['answer_type_id'])){
                    $data['answer_type_id'] = $input['answer_type_id'];
                }
                if(!empty($input['weightage'])){
                    $data['weightage'] = $input['weightage'];
                }
                if(!empty($input['passing_weightage'])) {
                    $data['passing_weightage'] = $input['passing_weightage'];
                }
                $data['updated_by'] = $employee->employeeSystemID;

                $evaluationDetails = $editOrAmend ?
                    EvaluationCriteriaDetailsEditLog::find($input['amd_id']) :
                    EvaluationCriteriaDetails::find($input['id']);

                $result = $evaluationDetails->update($data);

                if($result){

                    if($input['is_final_level'] == 1 && $input['critera_type_id'] == 2 && ($input['answer_type_id'] == 4 || $input['answer_type_id'] == 5) ){
                        $config = $editOrAmend ?
                            EvacuationCriteriaScoreConfigLog::getCriteriaBaseScore($input['amd_id']) :
                            EvaluationCriteriaScoreConfig::getCriteriaBaseScore($input['id']);
                        if(empty($config)){
                            return ['success' => false, 'message' => trans('srm_masters.at_least_one_score_configuration_is_required')];
                        }
                    }
                    return ['success' => true, 'message' => trans('srm_masters.successfully_updated')];
                }
                return ['success' => false, 'message' => trans('srm_masters.failed_to_update')];
            });
        } catch(\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    public function deleteEvaluationCriteria($input)
    {
        try{
            $versionID = $input['versionID'] ?? 0;
            $editOrAmend = $versionID > 0;
            $fromTender = $input['fromTender'] ?? false;

            return DB::transaction(function () use ($input, $editOrAmend, $fromTender) {

                $model = $editOrAmend ? EvaluationCriteriaDetailsEditLog::class : EvaluationCriteriaDetails::class;
                $scoreModel = $editOrAmend ? EvacuationCriteriaScoreConfigLog::class : EvaluationCriteriaScoreConfig::class;
                $idKey = $editOrAmend ? 'amd_id' : 'id';
                $criteriaID = $input[$idKey] ?? null;

                $evaluationDetails = $model::find($criteriaID);

                if (empty($evaluationDetails)) {
                    return ['success' => false, 'message' => trans('srm_masters.record_not_found')];
                }

                $result = $editOrAmend ? tap($evaluationDetails)->update(['is_deleted' => 1]) : $evaluationDetails->delete();

                $editOrAmend
                    ? $scoreModel::where(['fromTender' => $fromTender, 'criteria_detail_id' => $criteriaID, 'is_deleted' => 0])->update(['is_deleted' => 1])
                    : $scoreModel::where(['fromTender' => $fromTender, 'criteria_detail_id' => $criteriaID])->delete();

                $deleteChildren = function ($parentID, $level = 1) use (&$deleteChildren, $editOrAmend, $fromTender, $model, $scoreModel, $idKey) {
                    $children = $model::getChildCriteria($parentID);
                    foreach ($children as $child) {
                        $childID = $child[$idKey];

                        $deleteChildren($childID, $level + 1);

                        if ($editOrAmend) {
                            $model::where($idKey, $childID)->where('is_deleted', 0)->update(['is_deleted' => 1]);
                            $scoreModel::where(['fromTender' => $fromTender, 'criteria_detail_id' => $childID, 'is_deleted' => 0])->update(['is_deleted' => 1]);
                        } else {
                            $model::where($idKey, $childID)->delete();
                            $scoreModel::where(['fromTender' => $fromTender, 'criteria_detail_id' => $childID])->delete();
                        }
                    }
                };

                $deleteChildren($criteriaID);

                return ['success' => true, 'message' => trans('srm_masters.successfully_deleted'), 'data' => $result];
            });

        } catch(\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function validateWeightage($input){
        $weightage = $input['weightage'];
        $tenderMasterId = $input['tenderMasterId'];
        $versionID = $input['versionID'] ?? 0;
        $editOrAmend = $versionID > 0;
        $level  = $input['level'] ?? null;

        $parentId = $input['parentId'];
        if($level == 1){
            $result = $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::calculateWeightage($tenderMasterId, 1, $versionID) :
                EvaluationCriteriaDetails::calculateWeightage($tenderMasterId, 1);
            $total = $result + $weightage;
            if($total>100){
                return ['success' => false, 'message' => trans('srm_masters.total_weightage_cannot_exceed_100_percent')];
            } else {
                return ['success' => true, 'message' => trans('srm_masters.success')];
            }
        } else {
            $result = $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::calculateWeightage($tenderMasterId, $level, $versionID, $parentId) :
                EvaluationCriteriaDetails::calculateWeightage($tenderMasterId, $level, $parentId);

            $parent = $editOrAmend ?
                EvaluationCriteriaDetailsEditLog::find($parentId) :
                EvaluationCriteriaDetails::find($parentId);

            $total = $result + $weightage;

            if($total > $parent->weightage){
                return ['success' => false, 'message' => trans('srm_masters.total_child_weightage_cannot_exceed', [
                    'code' => $parent['weightage'],
                ])];
            }else{
                return ['success' => true, 'message' => trans('srm_masters.success')];
            }
        }
    }
    public function validateWeightageEdit($input){
        try{
            $validation = $this->validateWeightageRequest($input);
            if(!$validation['success']){
                return ['success' => false, 'message' => $validation['message']];
            }
            $versionID = $input['versionID'] ?? 0;
            $editOrAmend = $versionID > 0;

            if($input['level'] != 1){
                $result = $editOrAmend ?
                    EvaluationCriteriaDetailsEditLog::calculateWeightage($input['tender_id'], $input['level'], $versionID, $input['parent_id'], $input['amd_id']) :
                    EvaluationCriteriaDetails::calculateWeightage($input['tender_id'], $input['level'], $input['parent_id'], $input['id']);

                $parent = $editOrAmend ?
                    EvaluationCriteriaDetailsEditLog::getParentEvaluationCriteria($input['parent_id']) :
                    EvaluationCriteriaDetails::getParentEvaluationCriteria($input['parent_id']);

                if(empty($parent)){
                    return ['success' => false, 'message' => trans('srm_masters.parent_evaluation_record_not_found')];
                }

                $total = $result + $input['weightage'];

                if($total > $parent['weightage']){
                    return ['success' => false, 'message' => trans('srm_masters.total_child_weightage_cannot_exceed', [
                        'code' => $parent['weightage'],
                    ])];
                } else{
                    return ['success' => true, 'message' => trans('srm_masters.success')];
                }
            } else {
                return ['success' => true, 'message' => trans('srm_masters.success')];
            }
        } catch (\Exception $ex){
            return ['success' => false, 'message' => trans('srm_masters.unexpected_error') . $ex->getMessage()];
        }
    }
    public function validateWeightageRequest($input): array{
        $validator = Validator::make($input, [
            'tender_id' => 'required'
        ], [
            'tender_id.required' => 'Tender Master ID is required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => implode(', ', $validator->errors()->all())];
        }
        return ['success' => true, 'message' => 'Validation check success'];
    }
}
