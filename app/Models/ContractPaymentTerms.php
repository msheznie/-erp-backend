<?php

namespace App\Models;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractPaymentTerms extends Model
{
    use SoftDeletes;
    public $table = 'cm_payment_terms';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'uuid',
        'contract_id',
        'title',
        'description',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by'
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
        'title' => 'string',
        'description' => 'string',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public static function getContractPaymentTerms($contractID, $companySystemID)
    {
        return ContractPaymentTerms::select('uuid', 'title', 'description')
            ->where('contract_id', $contractID)
            ->where('company_id', $companySystemID)
            ->get();

    }
}
