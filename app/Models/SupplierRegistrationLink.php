<?php

namespace App\Models;

use Carbon\Carbon;
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
        'refferedBackYN',
        'is_bid_tender',
        'created_via',
        'sub_domain',
        'is_existing_erp_supplier'
    ];

    protected $appends = ['appointment_date_expired','uuid_notification'];

    protected $casts = [
        'id' => 'integer',
        'supplier_master_id' => 'integer',
        'uuid' => 'string',
        'is_existing_erp_supplier' => 'integer',
        ];

    public function supplier(){
        return $this->belongsTo(SupplierMaster::class, 'supplier_master_id','supplierCodeSystem');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    public function getAppointmentDateExpiredAttribute(): string
    {
        if (isset($this->attributes['token_expiry_date_time'])) {
            if ($this->attributes['token_expiry_date_time']) {
                $expireDate = Carbon::parse($this->attributes['token_expiry_date_time']);
                $today = Carbon::now();
               if($expireDate > $today){
                   return 'Active';
               } else {
                   return 'Expired';
               }
            } else {
                return 'N/A';
            }
        }

        return '';
    }

    public function getUuidNotificationAttribute()
    {
        if (isset($this->attributes['uuid'])) {
            return env("WEB_PUSH_APP_NAME_SRM")."_".$this->attributes['uuid'];
        }

        return '';
    }

    public static function getSupplierMasterId($supplierId, $companySystemId)
    {
        return SupplierRegistrationLink::select('supplier_master_id')
            ->where('id', $supplierId)
            ->where('company_id', $companySystemId)
            ->first();
    }
}
