<?php

namespace App\Models;
use Awobaz\Compoships\Compoships;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="PdcLogPrintedHistory",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="pdcLogID",
 *          description="pdcLogID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="chequePrintedBy",
 *          description="chequePrintedBy",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="chequePrintedDate",
 *          description="chequePrintedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class PdcLogPrintedHistory extends Model
{
    use Compoships;
    public $table = 'pdc_log_printed_history';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'pdcLogID',
        'chequePrintedBy',
        'changedBy',
        'documentSystemID',
        'documentmasterAutoID',
        'amount',
        'currencyID',
        'chequeNo',
        'chequePrintedDate'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'pdcLogID' => 'integer',
        'documentSystemID' => 'integer',
        'documentmasterAutoID' => 'integer',
        'currencyID' => 'integer',
        'chequeNo' => 'string',
        'amount' => 'float',
        'changedBy' => 'integer',
        'chequePrintedBy' => 'integer',
        'chequePrintedDate' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function cheque_printed_by()
    {
        return $this->belongsTo('App\Models\Employee','chequePrintedBy',  'employeeSystemID');
    }

    public function changed_by()
    {
        return $this->belongsTo('App\Models\Employee','changedBy',  'employeeSystemID');
    }

     public function pay_supplier() {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster','documentmasterAutoID',  'PayMasterAutoId');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster','currencyID',  'currencyID');
    }
}
