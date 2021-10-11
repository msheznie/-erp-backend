<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="UnbilledGrvGroupBy",
 *      required={""},
 *      @SWG\Property(
 *          property="unbilledgrvAutoID",
 *          description="unbilledgrvAutoID",
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
 *          property="selectedForBooking",
 *          description="selectedForBooking",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fullyBooked",
 *          description="fullyBooked",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvType",
 *          description="grvType",
 *          type="string"
 *      )
 * )
 */
class UnbilledGrvGroupBy extends Model
{

    public $table = 'erp_unbilledgrvgroupby';
    public $append = ['totalAmount'];

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'unbilledgrvAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'supplierID',
        'purchaseOrderID',
        'purhaseReturnAutoID',
        'grvAutoID',
        'logisticYN',
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
        'totalVATAmount',
        'totalVATAmountLocal',
        'totalVATAmountRpt',
        'isAddon',
        'selectedForBooking',
        'fullyBooked',
        'grvType',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'unbilledgrvAutoID' => 'integer',
        'companySystemID' => 'integer',
        'logisticYN' => 'integer',
        'companyID' => 'string',
        'supplierID' => 'integer',
        'purhaseReturnAutoID' => 'integer',
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
        'totalVATAmount' => 'float',
        'totalVATAmountLocal' => 'float',
        'totalVATAmountRpt' => 'float',
        'isAddon' => 'integer',
        'selectedForBooking' => 'integer',
        'fullyBooked' => 'integer',
        'grvType' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function transaction_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransactionCurrencyID', 'currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function pomaster()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderID', 'purchaseOrderID');
    }

    public function grvmaster()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'grvAutoID', 'grvAutoID');
    }


    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\BookInvSuppDet', 'unbilledgrvAutoID', 'unbilledgrvAutoID');
    }

    public function setGrvDateAttribute($value)
    {
        $this->attributes['grvDate'] = Helper::dateAddTime($value);
    }
}
