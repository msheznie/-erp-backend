<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SrmTenderDepartment extends Model
{
    public $table = 'srm_tender_department';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['tender_id', 'department_id', 'company_id', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'department_id' => 'integer',
        'company_id' => 'integer'
    ];

    public function department_master()
    {
        return $this->belongsTo(SrmDepartmentMaster::class, 'department_id', 'id');
    }
}