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

    public static function getTenderDepartmentEditLog($tenderID){
        return self::where('tender_id', $tenderID)->get();
    }

    public static function getTenderDepartmentList($tenderMasterId){
        return self::select('department_id as id', 'srm_department_master.description as itemName')
            ->leftJoin('srm_department_master', 'srm_department_master.id', '=', 'srm_tender_department.department_id')
            ->where('tender_id', $tenderMasterId)
            ->get();
    }
}
