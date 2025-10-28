<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BudgetDetailComment",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetDetailID",
 *          description="budgetDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class BudgetDetailComment extends Model
{

    public $table = 'budget_detail_comments';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'parentId',
        'budgetDetailID',
        'comment',
        'created_by',
        'is_resolved',
        'resolved_by',
        'resolved_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'parentId' => 'integer',
        'budgetDetailID' => 'integer',
        'comment' => 'string',
        'created_by' => 'integer',
        'is_resolved' => 'boolean',
        'resolved_by' => 'integer',
        'resolved_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by_emp()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    public function resolved_by_emp()
    {
        return $this->belongsTo('App\Models\Employee', 'resolved_by', 'employeeSystemID');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\BudgetDetailComment', 'parentId', 'id');
    }

}
