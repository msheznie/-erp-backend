<?php

namespace App\Models;
use App\Utilities\ContractManagementUtils;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractMilestonePaymentSchedules extends Model
{
    use SoftDeletes;
    public $table = 'cm_milestone_payment_schedules';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];

    protected $hidden = ['id', 'contract_id', 'milestone_id'];



    public $fillable = [
        'uuid',
        'contract_id',
        'milestone_id',
        'description',
        'percentage',
        'amount',
        'payment_due_date',
        'actual_payment_date',
        'milestone_due_date',
        'currency_id',
        'company_id',
        'created_by',
        'updated_by',
        'milestone_status'
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
        'milestone_id' => 'integer',
        'description' => 'string',
        'percentage' => 'float',
        'amount' => 'float',
        'payment_due_date' => 'string',
        'actual_payment_date' => 'string',
        'milestone_due_date' => 'string',
        'currency_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'milestone_status' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
    public function milestoneDetail()
    {
        return $this->belongsTo(ContractMilestone::class, 'milestone_id', 'id');
    }
    public function currency()
    {
        return $this->belongsTo(CurrencyMaster::class, 'currency_id', 'currencyID');
    }
    public function contractMaster()
    {
        return $this->belongsTo(ContractMaster::class, 'contract_id', 'id');
    }
    public static function milestonePaymentSchedules($companyId, $contractID)
    {
        return self::select('uuid', 'contract_id', 'milestone_id', 'description',
            'percentage', 'amount', 'payment_due_date', 'actual_payment_date', 'milestone_due_date', 'currency_id',
            'milestone_status')
            ->where('contract_id', $contractID)
            ->where('company_id', $companyId)
            ->with([
                'milestoneDetail' => function ($q)
                {
                    $q->select('id', 'uuid', 'title');
                },
                'currency' => function ($q)
                {
                    $q->select('currencyID', 'CurrencyCode', 'DecimalPlaces');
                }
            ]);
    }
}
