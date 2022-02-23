<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierRegistrationLink extends Model
{
    public $table = 'srm_supplier_registration_link';

    protected $primaryKey  = 'id';

    public $fillable = [
        'name',
        'email',
        'registration_number',
        'company_id',
        'token',
        'STATUS',
        'token_expiry_date_time',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'uuid',
        'supplier_master_id',
        'confirmed_by_emp_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved_by_emp_name',
        'approved_yn',
        'approved_by_emp_id',
        'RollLevForApp_curr',
        'timesReferred',
        'confirmed_yn',
        'refferedBackYN'
    ];

    protected $casts = [
        'id' => 'integer',
        'supplier_master_id' => 'integer',
        'uuid' => 'string',
        ];

    public function supplier(){
        return $this->belongsTo(SupplierMaster::class, 'supplier_master_id','supplierCodeSystem');
    }

    public function created_by()
    {
        return $this->hasOne('App\Models\SupplierAssigned', 'supplierCodeSytem', 'created_by');
    }
}
