<?php
/**
 * =============================================
 * -- File Name : UnbilledGRV.php
 * -- Project Name : ERP
 * -- Module Name :  Inventory
 * -- Author : Mohamed Mubashir
 * -- Create date : 30 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="UnbilledGRV",
 *      required={""},
 *      @SWG\Property(
 *          property="unbilledgrvAutoID",
 *          description="unbilledgrvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderID",
 *          description="purchaseOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyER",
 *          description="supplierTransactionCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totTransactionAmount",
 *          description="totTransactionAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totLocalAmount",
 *          description="totLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totRptAmount",
 *          description="totRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="isAddon",
 *          description="isAddon",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvType",
 *          description="grvType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isReturn",
 *          description="isReturn",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class UnbilledGRV extends Model
{

    public $table = 'erp_unbilledgrv';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'unbilledgrvAutoID';

    public $fillable = [
        'companyID',
        'supplierID',
        'purchaseOrderID',
        'grvAutoID',
        'grvDate',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'totTransactionAmount',
        'totLocalAmount',
        'totRptAmount',
        'isAddon',
        'grvType',
        'isReturn',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'unbilledgrvAutoID' => 'integer',
        'companyID' => 'string',
        'supplierID' => 'integer',
        'purchaseOrderID' => 'integer',
        'grvAutoID' => 'integer',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'totTransactionAmount' => 'float',
        'totLocalAmount' => 'float',
        'totRptAmount' => 'float',
        'isAddon' => 'integer',
        'grvType' => 'string',
        'isReturn' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function setGrvDateAttribute($value)
    {
        $this->attributes['grvDate'] = Helper::dateAddTime($value);
    }

}
