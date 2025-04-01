<?php

namespace App\Models;

use Eloquent as Model;

class EvaluationCriteriaMaster extends Model
{
    public $table = 'srm_evaluation_criteria_master';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'name',
        'is_active',
        'company_id',
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
        'name' => 'string',
        'is_active' => 'integer',
        'company_id' => 'integer',
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

    public function evaluation_criteria_type()
    {
        return $this->belongsTo('App\Models\EvaluationCriteriaType', 'critera_type_id', 'id');
    }

    public function evaluation_criteria_details()
    {
        return $this->hasMany('App\Models\EvaluationCriteriaDetails', 'evaluation_criteria_master_id', 'id');
    }

    public function evaluation_criteria_master_details()
    {
        return $this->hasMany('App\Models\EvaluationCriteriaMasterDetails', 'evaluation_criteria_master_id', 'id');
    }

}
