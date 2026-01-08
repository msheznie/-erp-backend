<?php

namespace App\Models;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractOverallPenalty extends Model
{
    use SoftDeletes;
    public $table = 'cm_overall_penalty';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'uuid',
        'contract_id',
        'minimum_penalty_percentage',
        'minimum_penalty_amount',
        'maximum_penalty_percentage',
        'maximum_penalty_amount',
        'actual_percentage',
        'actual_penalty_amount',
        'penalty_circulation_start_date',
        'actual_penalty_start_date',
        'penalty_circulation_frequency',
        'due_in',
        'due_penalty_amount',
        'actual_due_penalty_amount',
        'status',
        'company_id',
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
        'contract_id' => 'integer',
        'minimum_penalty_percentage' => 'float',
        'minimum_penalty_amount' => 'float',
        'maximum_penalty_percentage' => 'float',
        'maximum_penalty_amount' => 'float',
        'actual_percentage' => 'float',
        'actual_penalty_amount' => 'float',
        'penalty_circulation_start_date' => 'string',
        'actual_penalty_start_date' => 'string',
        'penalty_circulation_frequency' => 'integer',
        'due_in' => 'integer',
        'due_penalty_amount' => 'float',
        'actual_due_penalty_amount' => 'float',
        'status' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function billingFrequency()
    {
        return $this->belongsTo(BillingFrequencies::class, 'penalty_circulation_frequency', 'id');
    }

    public static function getOverallPenalty($contractID, $companyId)
    {
        return ContractOverallPenalty::select('uuid', 'minimum_penalty_percentage', 'minimum_penalty_amount',
            'maximum_penalty_percentage', 'maximum_penalty_amount', 'actual_percentage', 'actual_penalty_amount',
            'penalty_circulation_start_date', 'actual_penalty_start_date', 'penalty_circulation_frequency', 'due_in',
            'due_penalty_amount', 'actual_due_penalty_amount', 'status')
            ->with([
                'billingFrequency' => function ($q)
                {
                    $q->select('id', 'description');
                }
            ])
            ->where('contract_id', $contractID)
            ->where('company_id', $companyId)
            ->first();

    }
}
