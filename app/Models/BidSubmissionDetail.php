<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BidSubmissionDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bid_master_id",
 *          description="bid_master_id",
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
 *          property="evaluation_detail_id",
 *          description="evaluation_detail_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="score_id",
 *          description="score_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="score",
 *          description="score",
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
class BidSubmissionDetail extends Model
{

    public $table = 'srm_bid_submission_detail';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $timestamps = false;

    public $fillable = [
        'bid_master_id',
        'tender_id',
        'evaluation_detail_id',
        'score_id',
        'score',
        'result',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'eval_score',
        'eval_result',
        'evaluate_by',
        'evaluate_at',
        'bid_selection_id',
        'eval_score_id',
        'technical_ranking'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'bid_master_id' => 'integer',
        'tender_id' => 'integer',
        'evaluation_detail_id' => 'integer',
        'score_id' => 'integer',
        'result' => 'float',
        'score' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'eval_result' => 'float',
        'technical_ranking' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function srm_evaluation_criteria_details(){
        return $this->belongsTo('App\Models\EvaluationCriteriaDetails','evaluation_detail_id','id');
    }

    public function srm_tender_master(){
        return $this->belongsTo('App\Models\TenderMaster','tender_id','id');
    }

    public function supplier_registration_link()
    {
        return $this->belongsTo('App\Models\SupplierRegistrationLink', 'created_by', 'id');
    }

    public function srm_bid_submission_master(){
        return $this->belongsTo('App\Models\BidSubmissionMaster','bid_master_id','id');
    }

    public static function getBidSubmissionDetails($tender_id, $bidSubmissionMasterId){
        return self::select('evaluation_detail_id')->where('tender_id',$tender_id)
            ->where('bid_master_id',$bidSubmissionMasterId)->where('eval_result',null)->whereHas('srm_evaluation_criteria_details',function($q){
            $q->where('critera_type_id',2);
        })->first();
    }

    public static function hasExistingEvaluatedRecord($tender_id, $bidSubmissionMasterId, $evaluationDetailId)
    {
        return self::where('tender_id', $tender_id)
            ->where('bid_master_id', $bidSubmissionMasterId)->where('evaluation_detail_id', $evaluationDetailId)
            ->whereNotNull('eval_result')->whereHas('srm_evaluation_criteria_details', function ($q) {
                $q->where('critera_type_id', 2);
            })->exists();
    }
}
