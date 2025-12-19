<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ErpItemLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="itemLedgerAutoID",
 *          description="itemLedgerAutoID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="referenceNumber",
 *          description="referenceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseSystemCode",
 *          description="wareHouseSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inOutQty",
 *          description="inOutQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacLocalCurrencyID",
 *          description="wacLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wacLocal",
 *          description="wacLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wacRptCurrencyID",
 *          description="wacRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wacRpt",
 *          description="wacRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fromDamagedTransactionYN",
 *          description="fromDamagedTransactionYN",
 *          type="integer",
 *          format="int32"
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
 *      )
 * )
 */
class ErpItemLedger extends Model
{

    public $table = 'erp_itemledger';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'referenceNumber',
        'wareHouseSystemCode',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'inOutQty',
        'wacLocalCurrencyID',
        'wacLocal',
        'wacRptCurrencyID',
        'wacRpt',
        'comments',
        'transactionDate',
        'fromDamagedTransactionYN',
        'createdUserSystemID',
        'createdUserID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemLedgerAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'referenceNumber' => 'string',
        'wareHouseSystemCode' => 'integer',
        'itemSystemCode' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'unitOfMeasure' => 'integer',
        'inOutQty' => 'float',
        'wacLocalCurrencyID' => 'integer',
        'wacLocal' => 'float',
        'wacRptCurrencyID' => 'integer',
        'wacRpt' => 'float',
        'comments' => 'string',
        'fromDamagedTransactionYN' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string'
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
        return $this->belongsTo('App\Models\CurrencyMaster', 'wacLocalCurrencyID', 'currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'wacRptCurrencyID', 'currencyID');
    }

    public function service_line(){
        return $this->belongsTo('App\Models\ServiceLine', 'serviceLineSystemID','serviceLineSystemID');
    }

    public function warehouse(){
        return $this->belongsTo('App\Models\WarehouseMaster', 'wareHouseSystemCode','wareHouseSystemCode');
    }

    public function uom(){
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure','UnitID');
    }

    
    public function item_master(){
        return $this->belongsTo('App\Models\ItemMaster', 'itemSystemCode','itemCodeSystem');
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_itemledger.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_itemledger.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'supplierTransactionCurrencyID',$columnAs = 'CurrencyName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_itemledger.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_itemledger.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }

    public function scopeUnitJoin($q,$as = 'unit', $column = 'unitOfMeasure' , $columnAs = 'primarySupplierCode')
    {
        return $q->leftJoin('units as '.$as,$as.'.UnitID','erp_itemledger.'.$column);
    }

    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_itemledger.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }
}
