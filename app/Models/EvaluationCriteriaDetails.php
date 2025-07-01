<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

/**
 * @SWG\Definition(
 *      definition="EvaluationCriteriaDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="parent_id",
 *          description="parent_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="critera_type_id",
 *          description="critera_type_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="answer_type_id",
 *          description="answer_type_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="level",
 *          description="level",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_final_level",
 *          description="is_final_level",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="weightage",
 *          description="weightage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="passing_weightage",
 *          description="passing_weightage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sort_order",
 *          description="sort_order",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class EvaluationCriteriaDetails extends Model
{

    public $table = 'srm_evaluation_criteria_details';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['active'];


    public $fillable = [
        'tender_id',
        'parent_id',
        'description',
        'critera_type_id',
        'answer_type_id',
        'level',
        'is_final_level',
        'weightage',
        'passing_weightage',
        'min_value',
        'max_value',
        'sort_order',
        'evaluation_criteria_master_id',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'parent_id' => 'integer',
        'description' => 'string',
        'critera_type_id' => 'integer',
        'answer_type_id' => 'integer',
        'level' => 'integer',
        'is_final_level' => 'integer',
        'weightage' => 'float',
        'passing_weightage' => 'float',
        'min_value' => 'float',
        'max_value' => 'float',
        'sort_order' => 'integer',
        'evaluation_criteria_master_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
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
        return $this->hasMany('App\Models\EvaluationCriteriaDetails', 'parent_id', 'id');
    }

    public function evaluation_criteria_score_config()
    {
        return $this->hasMany('App\Models\EvaluationCriteriaScoreConfig', 'criteria_detail_id', 'id');
    }

    public function bid_submission_detail()
    {
        return $this->hasOne('App\Models\BidSubmissionDetail', 'evaluation_detail_id', 'id');
    }

    public function bid_submission_detail1()
    {
        return $this->hasMany('App\Models\BidSubmissionDetail', 'evaluation_detail_id', 'id');
    }

    public function evaluation_criteria_master()
    {
        return $this->belongsTo('App\Models\EvaluationCriteriaMaster', 'evaluation_criteria_master_id', 'id');
    }

    public function tender_master()
    {
        return $this->belongsTo('App\Models\TenderMaster', 'tender_id', 'id');
    }

    public static function getEvaluationCriteriaDetails($tenderId, $level, $criteriaType)
    {
        return EvaluationCriteriaDetails::where('tender_id', $tenderId)
            ->where('critera_type_id', $criteriaType)
            ->where('level', $level);
    }

    public static function getEvaluationCriteriaDetailForAmd($tender_id){
        return self::where('tender_id', $tender_id)->get();
    }
    public static function getEvaluationCriteriaDetailsList($tenderMasterId,$critera_type_id){
        return self::with(['evaluation_criteria_type','tender_criteria_answer_type','child'=> function($q){
            $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                $q->with(['evaluation_criteria_type','tender_criteria_answer_type','child' => function($q){
                    $q->with(['evaluation_criteria_type','tender_criteria_answer_type']);
                }]);
            }]);
        }])->where('tender_id', $tenderMasterId)->where('level',1)->where('critera_type_id', $critera_type_id)->get();
    }
    public static function getSortOrder($tenderMasterId, $level, $parent_id){
        return self::where('tender_id', $tenderMasterId)->where('level', $level)->where('parent_id', $parent_id)->orderBy('sort_order', 'desc')->first();
    }
    public static function checkForDescriptionDuplication($tenderMasterId, $description, $level, $id = 0){
        return self::where('tender_id', $tenderMasterId)->where('description', $description)->where('level', $level)
            ->when($id>0, function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })->first();
    }
    public static function getEvaluationDetailById($evaluationID){
        return self::with(['evaluation_criteria_score_config' => function ($q) {
            $q->where('fromTender', 1);
        }])
            ->where('id', $evaluationID)
            ->first();
    }
    public static function getChildCriteria($parent_id){
        return self::where('parent_id', $parent_id)->get();
    }
    public static function getCriteriaWithoutChildren($tenderMasterID, $versionID, $editOrAmend = false, $checkParentLevel = true)
    {
        $table = $editOrAmend ? 'srm_evaluation_criteria_details_edit_log' : 'srm_evaluation_criteria_details';
        $aliasParent = $checkParentLevel ? 'parent' : 'sub';
        $aliasChild = 'child';
        $parentIDCol = $editOrAmend ? 'amd_id' : 'id';
        $childParentIDCol = 'parent_id';

        $query = DB::table("$table as $aliasParent")
            ->leftJoin("$table as $aliasChild", "$aliasParent.$parentIDCol", '=', "$aliasChild.$childParentIDCol")
            ->where("$aliasParent.tender_id", $tenderMasterID)
            ->where("$aliasParent.critera_type_id", 2)
            ->where("$aliasParent.is_final_level", 0)
            ->whereNull("$aliasChild.$parentIDCol");

        $query->where("$aliasParent.parent_id", $checkParentLevel ? 0 : '!=', 0);

        if ($editOrAmend) {
            $query->where("$aliasParent.is_deleted", 0)
                ->where("$aliasParent.tender_version_id", $versionID)
                ->where(function($q) use ($aliasChild, $versionID, $childParentIDCol) {
                    $q->whereNull("$aliasChild.$childParentIDCol")
                        ->orWhere(function($q2) use ($aliasChild, $versionID) {
                            $q2->where("$aliasChild.is_deleted", 0)
                                ->where("$aliasChild.tender_version_id", $versionID);
                        });
                });
        }

        return $query->select("$aliasParent.*")->get();
    }
    public static function calculateWeightage($tenderMasterId, $level, $parentId = 0, $id = 0){
        return self::where('tender_id', $tenderMasterId)
            ->when($id > 0, function ($q) use ($id) {
                $q->where('id', '!=', $id);
            })
            ->where('level', $level)
            ->when($level > 1, function ($q) use ($parentId) {
                $q->where('parent_id',$parentId);
            })
            ->sum('weightage');
    }
    public static function getParentEvaluationCriteria($parentID){
        return self::where('id', $parentID)->first();
    }
}
