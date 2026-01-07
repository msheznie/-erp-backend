<?php

namespace App\Models;
use Eloquent as Model;

class ContractOverallRetention extends Model
{
    public $table = 'cm_overall_retention';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'uuid',
        'contractId',
        'contractAmount',
        'retentionPercentage',
        'retentionAmount',
        'startDate',
        'dueDate',
        'retentionWithholdPeriod',
        'companySystemId',
        'created_by',
        'updated_by'
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
        'contractAmount' => 'float',
        'retentionPercentage' => 'float',
        'retentionAmount' => 'float',
        'startDate' => 'string',
        'dueDate' => 'string',
        'retentionWithholdPeriod' => 'string',
        'companySystemId' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function contract()
    {
        return $this->belongsTo(ContractMaster::class, 'contractId', 'id');
    }

    public static function getContractOverall($contractId,$companyId)
    {
        return self::select('id', 'uuid', 'contractId', 'contractAmount', 'retentionPercentage', 'retentionAmount',
            'startDate', 'dueDate', 'retentionWithholdPeriod')
            ->where('companySystemId',$companyId)
            ->where('contractId',$contractId)
            ->first();
    }
}
