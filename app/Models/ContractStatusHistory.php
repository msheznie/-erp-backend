<?php

namespace App\Models;

use Eloquent as Model;

class ContractStatusHistory extends Model
{
    public $table = 'cm_contract_status_history';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'contract_id',
        'status',
        'company_id',
        'contract_history_id',
        'created_by',
        'updated_by',
        'system_user'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'contract_id' => 'integer',
        'contract_history_id' => 'integer',
        'status' => 'integer',
        'company_id' => 'integer',
        'system_user' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class,  'created_by', 'employeeSystemID');
    }

    public function contractHistory()
    {
        return $this->hasOne(ContractHistory::class, 'id', 'contract_history_id');
    }

    public function contractMaster()
    {
        return $this->hasOne(ContractMaster::class, 'id', 'contract_id');
    }

    public static function getContractStatusHistory($contractId)
    {
        return self::with(['employee' => function ($query)
        {
            $query->select('employeeSystemID','empName');
        },'contractHistory' => function ($query)
        {
            $query->select('id','contract_id')
                ->with(['contractMaster' => function ($q)
                {
                    $q->select('id','contractCode','title');
                }]);
        },'contractMaster'=> function ($q)
        {
            $q->select('id','contractCode','title');
        }])
            ->where('contract_id',$contractId)
            ->get()
            ->map(function ($contract) {
                return [
                    'status' => $contract->status,
                    'systemUser' => $contract->system_user,
                    'empName' => $contract->employee->empName ?? null,
                    'createdAt' => optional($contract->created_at)->format('d-m-Y'),
                    'contractCode' => $contract->contract_history_id
                        ? $contract->contractHistory->contractMaster->contractCode
                        : $contract->contractMaster->contractCode,
                    'title' => $contract->contract_history_id
                        ? $contract->contractHistory->contractMaster->title
                        : $contract->contractMaster->title,
                ];
            })
            ->toArray();
    }
}
