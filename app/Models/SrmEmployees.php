<?php

namespace App\Models;

use Eloquent as Model;

class SrmEmployees extends Model
{
    public $table = 'srm_employees';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'emp_id',
        'company_id',
        'is_active',
        'created_by',
        'created_at',
        'updated_at'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'emp_id' => 'integer',
        'company_id' => 'integer',
        'is_active' => 'integer',
        'created_by' => 'integer',
        'created_at' => 'date',
        'updated_at' => 'date'
    
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'emp_id');
    }
    public function tenderUserAccess()
    {
        return $this->hasOne('App\Models\SRMTenderUserAccess','user_id','emp_id');
    }
}
