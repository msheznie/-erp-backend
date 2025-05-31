<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="RecurringVoucherSetupScheDet",
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
 *          property="recurringVoucherAutoId",
 *          description="recurringVoucherAutoId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="recurringVoucherSheduleAutoId",
 *          description="recurringVoucherSheduleAutoId",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentID",
 *          description="documentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="detailProjectID",
 *          description="detailProjectID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companyID",
 *          description="companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="glAccount",
 *          description="glAccount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="glAccountDescription",
 *          description="glAccountDescription",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="comments",
 *          description="comments",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="debitAmount",
 *          description="debitAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="creditAmount",
 *          description="creditAmount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="contractUID",
 *          description="contractUID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="clientContractID",
 *          description="clientContractID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="isChecked",
 *          description="isChecked",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class RecurringVoucherSetupScheDet extends Model
{

    public $table = 'recurring_voucher_shedule_det';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    public $timestamps = false;




    public $fillable = [
        'recurringVoucherAutoId',
        'recurringVoucherSheduleAutoId',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'chartOfAccountSystemID',
        'currencyID',
        'detailProjectID',
        'companyID',
        'glAccount',
        'glAccountDescription',
        'comments',
        'debitAmount',
        'creditAmount',
        'serviceLineSystemID',
        'serviceLineCode',
        'contractUID',
        'clientContractID',
        'isChecked',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'recurringVoucherAutoId' => 'integer',
        'recurringVoucherSheduleAutoId' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'currencyID' => 'integer',
        'detailProjectID' => 'integer',
        'companyID' => 'string',
        'glAccount' => 'string',
        'glAccountDescription' => 'string',
        'comments' => 'string',
        'debitAmount' => 'float',
        'creditAmount' => 'float',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'contractUID' => 'integer',
        'clientContractID' => 'string',
        'isChecked' => 'boolean',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'integer',
        'createdPcID' => 'string',
        'createdDateTime' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        // 'recurringVoucherAutoId' => 'required',
        // 'recurringVoucherSheduleAutoId' => 'required',
        // 'documentSystemID' => 'required',
        // 'documentID' => 'required',
        // 'companySystemID' => 'required',
        // 'chartOfAccountSystemID' => 'required',
        // 'glAccount' => 'required',
        // 'glAccountDescription' => 'required',
        // 'isChecked' => 'required',
        // 'createdUserSystemID' => 'required',
        // 'createdUserID' => 'required',
        // 'createdPcID' => 'required',
        // 'createdDateTime' => 'required',
        // 'timestamp' => 'required'
    ];

     public function sheduleId()
    {
        return $this->belongsTo('App\Models\RecurringVoucherSetupSchedule', 'recurringVoucherSheduleAutoId','rrvSetupScheduleAutoID');
    }
}
