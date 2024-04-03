<?php

namespace App\Models;
use Eloquent as Model;

class MobileAccess extends Model
{
    public $table = 'mobile_access';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'name',
        'model',
        'serial_number',
        'employee',
        'is_active',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'model' => 'string',
        'serial_number' => 'string',
        'employee' => 'integer',
        'is_active' => 'boolean',
        'companySystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        //'model' => 'required',
        'serial_number' => 'required',
        //'employee' => 'required',
        'is_active' => 'required',
        'companySystemID' => 'required'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'employee','employeeSystemID');
    }
}
