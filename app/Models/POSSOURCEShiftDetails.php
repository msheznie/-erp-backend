<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSOURCEShiftDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="cashSales",
 *          description="cashSales",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cashSales_local",
 *          description="cashSales_local",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cashSales_reporting",
 *          description="cashSales_reporting",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="closingCashBalance_local",
 *          description="closingCashBalance_local",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="closingCashBalance_reporting",
 *          description="closingCashBalance_reporting",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="closingCashBalance_transaction",
 *          description="Opening Cash Balance + Cash Sales",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrency",
 *          description="companyLocalCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="companyLocalExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrency",
 *          description="companyReportingCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="companyReportingExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="counterID",
 *          description="counterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="different_local",
 *          description="different_local",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="different_local_reporting",
 *          description="different_local_reporting",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="different_transaction",
 *          description="different_transaction",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="endingBalance_local",
 *          description="transcation in local currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="endingBalance_reporting",
 *          description="transcation in reporting currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="endingBalance_transaction",
 *          description="ending transaction amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="endTime",
 *          description="endTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="giftCardTopUp",
 *          description="giftCardTopUp",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="giftCardTopUp_local",
 *          description="giftCardTopUp_local",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="giftCardTopUp_reporting",
 *          description="giftCardTopUp_reporting",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="id_store",
 *          description="id_store",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_sync",
 *          description="is_sync",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClosed",
 *          description="isClosed",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="shiftID",
 *          description="shiftID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="startingBalance_local",
 *          description="transcation in local currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="startingBalance_reporting",
 *          description="transcation in reporting currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="startingBalance_transaction",
 *          description="starting transaction amount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="startTime",
 *          description="startTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="transactionCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseID",
 *          description="wareHouseID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSOURCEShiftDetails extends Model
{

    public $table = 'pos_source_shiftdetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'cashSales',
        'cashSales_local',
        'cashSales_reporting',
        'closingCashBalance_local',
        'closingCashBalance_reporting',
        'closingCashBalance_transaction',
        'companyCode',
        'companyID',
        'companyLocalCurrency',
        'companyLocalCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalExchangeRate',
        'companyReportingCurrency',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingExchangeRate',
        'counterID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'different_local',
        'different_local_reporting',
        'different_transaction',
        'empID',
        'endingBalance_local',
        'endingBalance_reporting',
        'endingBalance_transaction',
        'endTime',
        'giftCardTopUp',
        'giftCardTopUp_local',
        'giftCardTopUp_reporting',
        'id_store',
        'is_sync',
        'isClosed',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'startingBalance_local',
        'startingBalance_reporting',
        'startingBalance_transaction',
        'startTime',
        'timestamp',
        'transaction_log_id',
        'transactionCurrency',
        'transactionCurrencyDecimalPlaces',
        'transactionCurrencyID',
        'transactionExchangeRate',
        'wareHouseID',
        'isSync'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'cashSales' => 'float',
        'cashSales_local' => 'float',
        'cashSales_reporting' => 'float',
        'closingCashBalance_local' => 'float',
        'closingCashBalance_reporting' => 'float',
        'closingCashBalance_transaction' => 'float',
        'companyCode' => 'string',
        'companyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalCurrencyDecimalPlaces' => 'integer',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalExchangeRate' => 'float',
        'companyReportingCurrency' => 'string',
        'companyReportingCurrencyDecimalPlaces' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingExchangeRate' => 'float',
        'counterID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPCID' => 'string',
        'createdUserGroup' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'different_local' => 'float',
        'different_local_reporting' => 'float',
        'different_transaction' => 'float',
        'empID' => 'integer',
        'endingBalance_local' => 'float',
        'endingBalance_reporting' => 'float',
        'endingBalance_transaction' => 'float',
        'endTime' => 'datetime',
        'giftCardTopUp' => 'float',
        'giftCardTopUp_local' => 'float',
        'giftCardTopUp_reporting' => 'float',
        'id_store' => 'integer',
        'is_sync' => 'integer',
        'isClosed' => 'boolean',
        'modifiedDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'shiftID' => 'integer',
        'startingBalance_local' => 'float',
        'startingBalance_reporting' => 'float',
        'startingBalance_transaction' => 'float',
        'startTime' => 'datetime',
        'timestamp' => 'datetime',
        'transaction_log_id' => 'integer',
        'transactionCurrency' => 'string',
        'transactionCurrencyDecimalPlaces' => 'integer',
        'transactionCurrencyID' => 'integer',
        'transactionExchangeRate' => 'float',
        'wareHouseID' => 'integer',
        'isSync' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id_store' => 'required',
        'isClosed' => 'required',
        'wareHouseID' => 'required'
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseID','wareHouseSystemCode');
    }

    public function menuSalesMasters()
    {
        return $this->hasMany(POSSourceMenuSalesMaster::class, 'shiftID', 'shiftID');
    }
}
