<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PdcLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentmasterAutoID",
 *          description="documentmasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentBankID",
 *          description="paymentBankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeRegisterAutoID",
 *          description="chequeRegisterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeNo",
 *          description="chequeNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeDate",
 *          description="chequeDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="chequeStatus",
 *          description="chequeStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class PdcLog extends Model
{

    public $table = 'erp_pdc_logs';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    protected $appends = ['chequeStatusValue'];

    public $fillable = [
        'documentSystemID',
        'documentmasterAutoID',
        'paymentBankID',
        'companySystemID',
        'currencyID',
        'chequeRegisterAutoID',
        'chequeNo',
        'chequeDate',
        'chequeStatus',
        'amount',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'documentmasterAutoID' => 'string',
        'paymentBankID' => 'integer',
        'companySystemID' => 'integer',
        'currencyID' => 'integer',
        'chequeRegisterAutoID' => 'integer',
        'chequeNo' => 'string',
        'chequeDate' => 'datetime',
        'chequeStatus' => 'integer',
        'amount' => 'float',
        'timestamp' => 'datetime'
    ];

    protected $statuses = array(
        "0" => 'Draft',
        "1" => 'Deposited',
        "2" => 'Returned',
        "3" => 'Done'
    );

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function getChequeStatusValueAttribute() {
       return $this->statuses[$this->chequeStatus];
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster','currencyID',  'currencyID');
    }



}
