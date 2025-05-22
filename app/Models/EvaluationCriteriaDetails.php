<?php

namespace App\Models;

use Eloquent as Model;

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

    public static function getEvaluationCriteriaDetails($tenderId, $level)
    {
        return EvaluationCriteriaDetails::where('tender_id', $tenderId)
            ->where('level', $level);
    }
    
}
