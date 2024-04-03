<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="RecurringVoucherSetupDetail",
 *      required={""},
 *      @OA\Property(
 *          property="rrvDetailAutoId",
 *          description="rrvDetailAutoId",
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
class RecurringVoucherSetupDetail extends Model
{

    public $table = 'recurring_voucher_setup_detail';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'rrvDetailAutoId';

    public $fillable = [
        'recurringVoucherAutoId',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'chartOfAccountSystemID',
        'currencyID',
        'glAccount',
        'glAccountDescription',
        'comments',
        'debitAmount',
        'creditAmount',
        'serviceLineSystemID',
        'serviceLineCode',
        'contractUID',
        'clientContractID',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'detail_project_id',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'rrvDetailAutoId' => 'integer',
        'recurringVoucherAutoId' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'currencyID' => 'integer',
        'glAccount' => 'string',
        'glAccountDescription' => 'string',
        'comments' => 'string',
        'debitAmount' => 'float',
        'creditAmount' => 'float',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'contractUID' => 'integer',
        'clientContractID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'integer',
        'detail_project_id' => 'integer',
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
    ];

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currencyID', 'currencyID');
    }

    public function chartofaccount()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function contract()
    {
        return $this->belongsTo('App\Models\Contract', 'contractUID', 'contractUID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\RecurringVoucherSetup', 'recurringVoucherAutoId', 'recurringVoucherAutoId');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'detail_project_id', 'id');
    }
    
}
