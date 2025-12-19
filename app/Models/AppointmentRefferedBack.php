<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class AppointmentRefferedBack extends Model
{
    use Compoships;
    public $table = 'appointmentRefferedBackHistory';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'appointment_id',
        'company_id',
        'created_by',
        'slot_detail_id',
        'status',
        'supplier_id',
        'tenat_id',
        'document_id',
        'document_system_id',
        'serial_no',
        'primary_code',
        'confirmed_by_emp_id',
        'confirmedByName',
        'confirmedByEmpID',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'RollLevForApp_curr',
        'timesReferred',
        'confirmed_yn',
        'refferedBackYN',
        'amended'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'appointment_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'slot_detail_id' => 'integer',
        'status' => 'integer',
        'supplier_id' => 'integer',
        'tenat_id' => 'integer',
        'document_id' => 'varchar',
        'document_system_id' => 'integer',
        'serial_no' => 'integer',
        'primary_code' => 'varchar',
        'confirmed_by_emp_id' => 'integer',
        'confirmedByName' => 'varchar',
        'confirmedByEmpID' => 'varchar',
        'confirmed_date' => 'datetime',
        'approved_yn' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_emp_name' => 'varchar',
        'approved_by_emp_id' => 'integer',
        'current_level_no' => 'integer',
        'timesReferred' => 'integer',
        'confirmed_yn' => 'integer',
        'amended' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
}
