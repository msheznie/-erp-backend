<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

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
    public function tenderUserAccessEditLog(){
        return $this->hasOne('App\Models\SrmTenderUserAccessEditLog','user_id','emp_id');
    }

    public static function getEmployeesDetails($companyId, $existingEmployeeIDs){
        return self::where('company_id', $companyId)
            ->where('is_active',true)->whereNotIn('emp_id',$existingEmployeeIDs)
            ->whereHas('employee', function ($query) {
                $query->where('empActive', 1)->where('discharegedYN','!=',-1);
            })->with('employee')->get();
    }
    public static function tenderUserAccessData($tenderId,$companyId,$moduleId, $requestData){
        return self::select('id', 'emp_id', 'company_id', 'is_active')
            ->whereHas('employee', function ($query) {
                $query->where('empActive', 1)->where('discharegedYN','!=',-1);
            })
            ->with(['employee' => function ($q) {
                $q->select('employeeSystemID', DB::raw("CONCAT(empID, ' | ', empFullName) as empFullDetails"));
            }])
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->when($requestData['enableRequestChange'], function ($q) use ($tenderId, $companyId, $moduleId, $requestData) {
                $q->whereDoesntHave('tenderUserAccessEditLog', function ($query) use ($tenderId, $companyId, $moduleId, $requestData) {
                    $query->where('tender_id', $tenderId)
                        ->where('company_id', $companyId)
                        ->where('version_id', $requestData['versionID'])
                        ->where('module_id', $moduleId)
                        ->where('is_deleted', 0);
                });
            })
            ->when(!$requestData['enableRequestChange'], function ($q) use ($tenderId, $companyId, $moduleId) {
                $q->whereDoesntHave('tenderUserAccess', function ($query) use ($tenderId, $companyId, $moduleId) {
                    $query->where('tender_id', $tenderId)
                        ->where('company_id', $companyId)
                        ->where('module_id', $moduleId);
                });
            })
            ->get();
    }
}
