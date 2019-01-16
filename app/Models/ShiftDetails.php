<?php
/**
 * =============================================
 * -- File Name : ShiftDetails.php
 * -- Project Name : ERP
 * -- Module Name : Shift Details
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ShiftDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="shiftID",
 *          description="shiftID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseID",
 *          description="wareHouseID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="counterID",
 *          description="counterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClosed",
 *          description="isClosed",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="cashSales",
 *          description="cashSales",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="giftCardTopUp",
 *          description="giftCardTopUp",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="startingBalance_transaction",
 *          description="startingBalance_transaction",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="endingBalance_transaction",
 *          description="endingBalance_transaction",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="different_transaction",
 *          description="different_transaction",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cashSales_local",
 *          description="cashSales_local",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="giftCardTopUp_local",
 *          description="giftCardTopUp_local",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="startingBalance_local",
 *          description="startingBalance_local",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="endingBalance_local",
 *          description="endingBalance_local",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="different_local",
 *          description="different_local",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cashSales_reporting",
 *          description="cashSales_reporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="giftCardTopUp_reporting",
 *          description="giftCardTopUp_reporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="closingCashBalance_transaction",
 *          description="closingCashBalance_transaction",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="closingCashBalance_local",
 *          description="closingCashBalance_local",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="startingBalance_reporting",
 *          description="startingBalance_reporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="endingBalance_reporting",
 *          description="endingBalance_reporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="different_local_reporting",
 *          description="different_local_reporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="closingCashBalance_reporting",
 *          description="closingCashBalance_reporting",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="transactionCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
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
 *          property="companyLocalCurrency",
 *          description="companyLocalCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="companyLocalExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
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
 *          property="companyReportingCurrency",
 *          description="companyReportingCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="companyReportingExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentCode",
 *          description="segmentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
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
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
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
 *      )
 * )
 */
class ShiftDetails extends Model
{

    public $table = 'erp_gpos_shiftdetails';
    

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';
    protected $primaryKey  = 'shiftID';


    public $fillable = [
        'wareHouseID',
        'empID',
        'counterID',
        'startTime',
        'endTime',
        'isClosed',
        'cashSales',
        'giftCardTopUp',
        'startingBalance_transaction',
        'endingBalance_transaction',
        'different_transaction',
        'cashSales_local',
        'giftCardTopUp_local',
        'startingBalance_local',
        'endingBalance_local',
        'different_local',
        'cashSales_reporting',
        'giftCardTopUp_reporting',
        'closingCashBalance_transaction',
        'closingCashBalance_local',
        'startingBalance_reporting',
        'endingBalance_reporting',
        'different_local_reporting',
        'closingCashBalance_reporting',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingCurrencyDecimalPlaces',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp',
        'id_store',
        'is_sync'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'shiftID' => 'integer',
        'wareHouseID' => 'integer',
        'empID' => 'integer',
        'counterID' => 'integer',
        'isClosed' => 'boolean',
        'cashSales' => 'float',
        'giftCardTopUp' => 'float',
        'startingBalance_transaction' => 'float',
        'endingBalance_transaction' => 'float',
        'different_transaction' => 'float',
        'cashSales_local' => 'float',
        'giftCardTopUp_local' => 'float',
        'startingBalance_local' => 'float',
        'endingBalance_local' => 'float',
        'different_local' => 'float',
        'cashSales_reporting' => 'float',
        'giftCardTopUp_reporting' => 'float',
        'closingCashBalance_transaction' => 'float',
        'closingCashBalance_local' => 'float',
        'startingBalance_reporting' => 'float',
        'endingBalance_reporting' => 'float',
        'different_local_reporting' => 'float',
        'closingCashBalance_reporting' => 'float',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionExchangeRate' => 'float',
        'transactionCurrencyDecimalPlaces' => 'integer',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalExchangeRate' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingExchangeRate' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'id_store' => 'integer',
        'is_sync' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\Employee', 'empID', 'employeeSystemID');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseID','wareHouseSystemCode');
    }

    public function counter()
    {
        return $this->belongsTo('App\Models\Counter','counterID','counterID');
    }

}
