<?php

namespace App\Models;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractPeriodicBillings extends Model
{
    use SoftDeletes;
    public $table = 'cm_periodic_billing';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'uuid',
        'contract_id',
        'amount',
        'start_date',
        'end_date',
        'occurrence_type',
        'due_in',
        'no_of_installment',
        'inst_payment_amount',
        'currency_id',
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
        'amount' => 'float',
        'start_date' => 'string',
        'end_date' => 'string',
        'occurrence_type' => 'integer',
        'due_in' => 'integer',
        'no_of_installment' => 'integer',
        'inst_payment_amount' => 'float',
        'currency_id' => 'integer',
        'company_id' => 'integer',
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
    public function currency()
    {
        return $this->belongsTo(CurrencyMaster::class, 'currency_id', 'currencyID');
    }

    public function billingFrequency()
    {
        return $this->belongsTo(ContractBillingFrequencies::class, 'occurrence_type', 'id');
    }
    public static function getContractPeriodicBilling($contractID)
    {
        return self::select('uuid', 'amount', 'start_date', 'end_date', 'occurrence_type', 'due_in',
            'no_of_installment', 'inst_payment_amount', 'currency_id')
            ->with([
                'currency' => function ($q)
                {
                    $q->select('currencyID', 'CurrencyCode', 'DecimalPlaces');
                },
                'billingFrequency' => function ($q)
                {
                    $q->select('id', 'description');
                }
            ])
            ->where('contract_id', $contractID)->first();
    }
}
