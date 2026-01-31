<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

class ContractUserAssign extends Model
{
    public $table = 'cm_contract_user_assign';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    protected $hidden = ['id', 'userGroupId', 'userId', 'createdBy', 'updatedBy'];

    public $fillable = [
        'uuid',
        'contractId',
        'userGroupId',
        'userId',
        'status',
        'createdBy',
        'updatedBy',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractId' => 'integer',
        'userGroupId' => 'integer',
        'userId' => 'integer',
        'status' => 'integer',
        'createdBy' => 'integer',
        'updatedBy' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public static function isExistingRecord($contractId, $userGroupId, $userId)
    {
        return ContractUserAssign::select('uuid', 'userGroupId')
            ->where('contractId', $contractId)
            ->where('userGroupId', $userGroupId)
            ->where('userId', $userId)
            ->where('status', 1)
            ->first();
    }

    public function userGroup()
    {
        return $this->belongsTo('App\Models\ContractUserGroup','userGroupId','id');
    }

    public function assignedUsers()
    {
        return $this->hasOne('App\Models\ContractUsers', 'id', 'userId');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class,  'createdBy', 'employeeSystemID');
    }
    public function updatedByEmployee()
    {
        return $this->belongsTo(Employee::class, 'updatedBy', 'employeeSystemID');
    }

    public static function getAssignedUsers($companySystemId, $uuid)
    {
        $contractResults = ContractMaster::select('id')->where('uuid', $uuid)->first();
        $subquery = ContractUserAssign::select('userGroupId', DB::raw('MIN(id) as min_id'))
            ->where('userGroupId', '!=', 0)
            ->where('contractId', $contractResults->id)
            ->groupBy('userGroupId');

        $distinctRecords = ContractUserAssign::select(
            'cm_contract_user_assign.id',
            'contractId',
            'cm_contract_user_assign.userGroupId',
            'userId',
            'created_at',
            'updated_at',
            'status',
            'updatedBy',
            'createdBy',
            'cm_contract_user_assign.uuid'
        )
            ->with([
                'userGroup' => function ($q) {
                    $q->select('id', 'groupName', 'uuid');
                },
                'assignedUsers' => function ($q) {
                    $q->select('id', 'contractUserName');
                },
                'employee' => function ($q) {
                    $q->select('employeeSystemID', 'empFullName');
                },
                'updatedByEmployee' => function ($q) {
                    $q->select('employeeSystemID', 'empFullName');
                }
            ])
            ->join(
                DB::raw("({$subquery->toSql()}) as sub"),
                function ($join) {
                    $join->on('cm_contract_user_assign.userGroupId', '=', 'sub.userGroupId')
                        ->on('cm_contract_user_assign.id', '=', 'sub.min_id');
                }
            )
            ->mergeBindings($subquery->getQuery());

        $allRecords = ContractUserAssign::select('id', 'contractId', 'cm_contract_user_assign.userGroupId', 'userId', 'created_at', 'updated_at',
            'status', 'updatedBy', 'createdBy', 'uuid')
            ->with(['userGroup' => function ($q2)
            {
                $q2->select('id', 'groupName', 'uuid');
            }, 'assignedUsers' => function ($q3)
            {
                $q3->select('id', 'contractUserName');
            }, 'employee' => function ($q4)
            {
                $q4->select('employeeSystemID', 'empName');
            }, 'updatedByEmployee' => function ($q5)
            {
                $q5->select('employeeSystemID', 'empName');
            }])
            ->where('cm_contract_user_assign.userGroupId', '=', 0)
            ->where('contractId', $contractResults->id)
            ->orderBy('id', 'desc');

        return $distinctRecords->union($allRecords);
    }
}
