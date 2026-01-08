<?php

namespace App\Models;
use Eloquent as Model;

class ContractMilestoneRetention extends Model
{
    public $table = 'cm_milestone_retention';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['id'];



    public $fillable = [
        'uuid',
        'contractId',
        'milestoneId',
        'retentionPercentage',
        'retentionAmount',
        'startDate',
        'dueDate',
        'withholdPeriod',
        'paymentStatus',
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
        'milestoneId' => 'integer',
        'retentionPercentage' => 'float',
        'retentionAmount' => 'float',
        'withholdPeriod' => 'string',
        'paymentStatus' => 'boolean',
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

    public function milestone()
    {
        return $this->belongsTo(ContractMilestone::class, 'milestoneId', 'id');
    }

    public static function ContractMilestoneRetention($companySystemID, $contractId)
    {
        return ContractMilestoneRetention::select('id', 'uuid', 'milestoneId', 'retentionPercentage', 'retentionAmount',
            'startDate', 'dueDate', 'withholdPeriod', 'paymentStatus')
            ->with([
                'milestone' => function ($q)
                {
                    $q->select('title', 'id', 'uuid')
                        ->with([
                            'milestonePaymentSchedules' => function ($q1)
                            {
                                $q1->select('amount', 'id', 'uuid','milestone_id');
                            }
                        ]);
                },
            ])->where('contractId', $contractId)
            ->where('companySystemId', $companySystemID)
            ->orderBy('id', 'asc');
    }
}
