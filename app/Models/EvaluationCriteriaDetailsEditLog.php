<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="EvaluationCriteriaDetailsEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="answer_type_id",
 *          description="answer_type_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="critera_type_id",
 *          description="critera_type_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="is_final_level",
 *          description="is_final_level",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="level",
 *          description="level",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="master_id",
 *          description="master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="max_value",
 *          description="max_value",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="min_value",
 *          description="min_value",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="modify_type",
 *          description="modify_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="passing_weightage",
 *          description="passing_weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="ref_log_id",
 *          description="ref_log_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="sort_order",
 *          description="sort_order",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="weightage",
 *          description="weightage",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      )
 * )
 */
class EvaluationCriteriaDetailsEditLog extends Model
{

    public $table = 'srm_evaluation_criteria_details_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';
    protected $appends = ['active'];

    public $fillable = [
        'id',
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
        'weightage',
        'tender_version_id',
        'created_by',
        'updated_by',
        'level_no',
        'is_deleted',
        'evaluation_criteria_master_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'answer_type_id' => 'integer',
        'critera_type_id' => 'integer',
        'description' => 'string',
        'id' => 'integer',
        'is_final_level' => 'integer',
        'level' => 'integer',
        'master_id' => 'integer',
        'max_value' => 'float',
        'min_value' => 'float',
        'modify_type' => 'integer',
        'parent_id' => 'integer',
        'passing_weightage' => 'float',
        'ref_log_id' => 'integer',
        'sort_order' => 'integer',
        'tender_id' => 'integer',
        'weightage' => 'float',
        'level_no' => 'integer',
        'is_deleted' => 'integer',
        'evaluation_criteria_master_id' => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    public function getActiveAttribute(){
        return false;
    }
    public function evaluation_criteria_type()
    {
        return $this->belongsTo('App\Models\EvaluationCriteriaType', 'critera_type_id', 'id');
    }

    public function tender_criteria_answer_type()
    {
        return $this->belongsTo('App\Models\TenderCriteriaAnswerType', 'answer_type_id', 'id');
    }

    public function child()
    {
        return $this->hasMany('App\Models\EvaluationCriteriaDetailsEditLog', 'parent_id', 'amd_id');
    }
    public function evaluation_criteria_score_config()
    {
        return $this->hasMany('App\Models\EvacuationCriteriaScoreConfigLog', 'criteria_detail_id', 'amd_id');
    }
    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }

    public static function getEvaluationCriteriaDetailsLog($tenderId, $level, $criteriaType, $versionID){
        return EvaluationCriteriaDetailsEditLog::where('tender_id', $tenderId)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('critera_type_id', $criteriaType)
            ->where('level', $level);
    }

    public static function getEvaluationCriteriaDetailsList($tenderMasterID, $criteriaTypeID, $versionID){
        return self::with(['evaluation_criteria_type','tender_criteria_answer_type','child'=> function($q) use ($versionID){
            $q->where('tender_version_id', $versionID)
                ->where('is_deleted', 0);
            $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q)use ($versionID){
                $q->where('tender_version_id', $versionID)
                    ->where('is_deleted', 0);
                $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q)use ($versionID){
                    $q->where('tender_version_id', $versionID)
                        ->where('is_deleted', 0);
                    $q->with(['evaluation_criteria_type','tender_criteria_answer_type']);
                }]);
            }]);
        }])->where('tender_id', $tenderMasterID)
            ->where('level', 1)
            ->where('critera_type_id', $criteriaTypeID)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)->get();
    }
    public static function getSortOrder($tenderMasterId, $level, $parent_id, $versionID){
        return self::where('tender_id', $tenderMasterId)
            ->where('level', $level)
            ->where('parent_id', $parent_id)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)
            ->orderBy('sort_order', 'desc')
            ->first();
    }
    public static function checkForDescriptionDuplication($tenderMasterId, $description, $level, $versionID, $id = 0){
        return self::where('tender_id', $tenderMasterId)
            ->when($id . 0, function ($q) use ($id) {
                $q->where('amd_id', '!=', $id);
            })
            ->where('description', $description)
            ->where('level', $level)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
    public static function getEvaluationDetailById($evaluationID, $versionID){
        return self::with(['evaluation_criteria_score_config' => function ($q) {
            $q->where('fromTender', 1)
                ->where('is_deleted', 0);
        }])
            ->where('amd_id', $evaluationID)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
    public static function getAmdID($parent_id){
        return self::where('id', $parent_id)->orderBy('amd_id', 'desc')->first();
    }
    public static function getChildCriteria($parent_id){
        return self::where('parent_id', $parent_id)->where('is_deleted', 0)->get();
    }

    public static function getAmendRecords($tenderID, $versionID, $onlyNullRecords){
        return self::where('tender_id', $tenderID)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();
    }
    public static function calculateWeightage($tenderMasterId, $level, $versionID, $parentId = 0, $amd_id = 0){
        return self::where('tender_id', $tenderMasterId)
            ->when($amd_id > 0, function ($q) use ($amd_id) {
                $q->where('amd_id', '!=', $amd_id);
            })
            ->where('level', $level)
            ->where('tender_version_id', $versionID)
            ->where('is_deleted', 0)
            ->when($level > 1, function ($q) use ($parentId) {
                $q->where('parent_id',$parentId);
            })
            ->sum('weightage');
    }
    public static function getParentEvaluationCriteria($parentID){
        return self::where('amd_id', $parentID)->first();
    }
}
