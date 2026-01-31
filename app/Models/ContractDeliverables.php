<?php

namespace App\Models;
use Eloquent as Model;

class ContractDeliverables extends Model
{
    public $table = 'cm_contract_deliverables';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public $fillable = [
        'uuid',
        'contractID',
        'milestoneID',
        'title',
        'description',
        'companySystemID',
        'created_by',
        'updated_by',
        'dueDate'
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
        'milestoneID' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'dueDate' => 'string',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function milestone()
    {
        return $this->belongsTo(ContractMilestone::class, 'milestoneID', 'id');
    }

    public static function getDeliverables($contractID, $companySystemID, $financeYN=0)
    {
        return ContractDeliverables::select('uuid', 'milestoneID', 'title', 'description', 'dueDate')
            ->with([
                'milestone' => function ($q)
                {
                    $q->select('id', 'uuid', 'title', 'due_date');
                }
            ])
            ->when($financeYN == 1, function ($q)
            {
                $q->where(function ($q)
                {
                    $q->whereHas('milestone');
                });
            })
            ->where('contractID', $contractID)
            ->where('companySystemID', $companySystemID)
            ->get();
    }
}
