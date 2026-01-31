<?php

namespace App\Models;
use Eloquent as Model;

class ContractMilestone extends Model
{
    public $table = 'cm_contract_milestone';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $hidden = ['id', 'contractID', 'created_at', 'updated_at'];

    public $fillable = [
        'uuid',
        'contractID',
        'title',
        'status',
        'companySystemID',
        'created_by',
        'updated_by',
        'description',
        'due_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractID' => 'integer',
        'title' => 'string',
        'status' => 'integer',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'description' => 'string',
        'due_date' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function milestonePaymentSchedules()
    {
        return $this->hasOne('App\Models\ContractMilestonePaymentSchedules', 'milestone_id', 'id');
    }

    public function milestonePenalty()
    {
        return $this->hasOne('App\Models\ContractMilestonePenaltyDetail', 'milestone_title', 'id');
    }

    public static function getContractMilestone($contractID, $companySystemID)
    {
        return ContractMilestone::select('uuid', 'title', 'description', 'due_date', 'status')
            ->where('contractID', $contractID)
            ->where('companySystemID', $companySystemID)
            ->get();

    }
}
