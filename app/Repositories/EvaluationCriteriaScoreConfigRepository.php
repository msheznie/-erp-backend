<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\EvacuationCriteriaScoreConfigLog;
use App\Models\EvaluationCriteriaDetails;
use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Models\EvaluationCriteriaMasterDetails;
use App\Models\EvaluationCriteriaScoreConfig;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EvaluationCriteriaScoreConfigRepository
 * @package App\Repositories
 * @version April 28, 2022, 2:30 pm +04
 *
 * @method EvaluationCriteriaScoreConfig findWithoutFail($id, $columns = ['*'])
 * @method EvaluationCriteriaScoreConfig find($id, $columns = ['*'])
 * @method EvaluationCriteriaScoreConfig first($columns = ['*'])
*/
class EvaluationCriteriaScoreConfigRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'criteria_detail_id',
        'label',
        'score',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EvaluationCriteriaScoreConfig::class;
    }
    public function updateCriteriaScore($input){
        try{
            return DB::transaction(function () use ($input) {
                $employee = Helper::getEmployeeInfo();
                $editOrAmend = $input['editOrAmend'] ?? false;
                $versionID = $input['versionID'] ?? 0;
                $ScoreConfig = $editOrAmend ?
                    EvacuationCriteriaScoreConfigLog::find($input['id']) :
                    EvaluationCriteriaScoreConfig::find($input['id'])->first();

                if(empty($ScoreConfig)){
                    return ['success' => false, 'message' => 'Evaluation criteria score not found'];
                }

                $data['score']=$input['score'];
                $data['updated_by'] = $employee->employeeSystemID;
                $result = $editOrAmend ?
                    EvacuationCriteriaScoreConfigLog::where('amd_id', $input['id'])->update($data) :
                    EvaluationCriteriaScoreConfig::where('id',$input['id'])->update($data);

                if($result && !$editOrAmend){
                    $x=1;
                    $min_value = 0;
                    $max_value = 0;
                    $criteriaConfig = EvaluationCriteriaScoreConfig::where('criteria_detail_id',$ScoreConfig['criteria_detail_id'])->get();
                    foreach ($criteriaConfig as $val){
                        if($x==1){
                            $min_value = $val['score'];
                        }
                        if($val['score']>$max_value){
                            $max_value = $val['score'];
                        }
                        if($val['score']<$min_value){
                            $min_value = $val['score'];
                        }
                        $ans['max_value'] = $max_value;
                        $ans['min_value'] = $min_value;
                        EvaluationCriteriaMasterDetails::where('id',$ScoreConfig['criteria_detail_id'])->update($ans);
                        $x++;
                    }
                } else {
                    return ['success' => false, 'message' => 'Update failed'];
                }
                return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => $exception];
        }
    }
    public function addEvaluationCriteriaConfig($input){
        try{
            return DB::transaction(function () use($input){
                $fromTender = $input['fromTender'] ?? false;
                $editOrAmend = $input['editOrAmend'] ?? false;
                $versionID = $input['versionID'] ?? 0;
                $employee = Helper::getEmployeeInfo();
                $min_value = 0;
                $max_value = 0;
                $x=1;
                $model = $fromTender ? EvaluationCriteriaDetails::class : EvaluationCriteriaMasterDetails::class;

                $drop = [
                    'criteria_detail_id' => $input['criteria_detail_id'],
                    'label'              => $input['label'],
                    'score'              => $input['score'],
                    'fromTender'         => $fromTender,
                    'created_by'         => $employee->employeeSystemID,
                ];

                if($editOrAmend){
                    $drop['id'] = null;
                    $drop['level_no'] = 1;
                    $drop['version_id'] = $versionID;
                    $result = EvacuationCriteriaScoreConfigLog::create($drop);
                } else {
                    $result = EvaluationCriteriaScoreConfig::create($drop);
                }

                if($result) {

                    $criteriaConfig = EvaluationCriteriaScoreConfig::where('fromTender', $fromTender)
                        ->where('criteria_detail_id', $input['criteria_detail_id'])->get();

                    foreach ($criteriaConfig as $val) {
                        if ($x == 1) {
                            $min_value = $val['score'];
                        }

                        if ($val['score'] > $max_value) {
                            $max_value = $val['score'];
                        }

                        if ($val['score'] < $min_value) {
                            $min_value = $val['score'];
                        }

                        $ans['max_value'] = $max_value;
                        $ans['min_value'] = $min_value;
                        if($editOrAmend) {
                            EvaluationCriteriaDetailsEditLog::where('amd_id', $input['criteria_detail_id'])->update($ans);
                        } else {
                            $model::where('id', $input['criteria_detail_id'])->update($ans);
                        }
                        $x++;
                    }
                    return ['success' => true, 'message' => 'Successfully Created'];
                }else {
                    return ['success' => false, 'message' => 'Failed to create',];
                }

            });
        } catch(\Exception $exception){
            return ['success' => false, 'message' => $exception];
        }
    }
}
