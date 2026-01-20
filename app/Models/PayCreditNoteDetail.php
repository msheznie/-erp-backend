<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="PayCreditNoteDetail",
 *      required={""},
 *      @OA\Property(
 *          property="payCreditNoteDetailAutoID",
 *          description="payCreditNoteDetailAutoID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="creditNoteAutoID",
 *          description="creditNoteAutoID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="creditNotePaymentAmount",
 *          description="creditNotePaymentAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
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
class PayCreditNoteDetail extends Model
{

    public $table = 'erp_paycreditnotedetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'payCreditNoteDetailAutoID';



    public $fillable = [
        'PayMasterAutoId',
        'creditNoteAutoID',
        'companySystemID',
        'creditNotePaymentAmount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'payCreditNoteDetailAutoID' => 'integer',
        'PayMasterAutoId' => 'integer',
        'creditNoteAutoID' => 'integer',
        'companySystemID' => 'integer',
        'creditNotePaymentAmount' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'PayMasterAutoId' => 'required',
        'creditNoteAutoID' => 'required',
        'companySystemID' => 'required',
        'creditNotePaymentAmount' => 'required'
    ];

    public function master()
    {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'PayMasterAutoId', 'PayMasterAutoId');
    }

    public function creditnote()
    {
        return $this->belongsTo('App\Models\CreditNote', 'creditNoteAutoID', 'creditNoteAutoID');
    }
}
