<?php

namespace App\Models;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractMilestonePenaltyMaster extends Model
{
    use SoftDeletes;

    public $table = 'cm_milestone_penalty_master';

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

    public static function getMilestonePenaltyMaster($contractID, $companyId)
    {
        return ContractMilestonePenaltyMaster::select('id','uuid','minimum_penalty_percentage','minimum_penalty_amount',
            'maximum_penalty_percentage', 'maximum_penalty_amount')
            ->where('contract_id', $contractID)
            ->where('company_id', $companyId)
            ->first();

    }
}
