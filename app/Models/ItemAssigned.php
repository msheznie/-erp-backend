<?php
/**
 * =============================================
 * -- File Name : ItemAssigned.php
 * -- Project Name : ERP
 * -- Module Name :  Item Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ItemAssigned
 * @package App\Models
 * @version March 9, 2018, 11:24 am UTC
 *
 * @property integer itemCodeSystem
 * @property string itemPrimaryCode
 * @property string secondaryItemCode
 * @property string barcode
 * @property string itemDescription
 * @property integer itemUnitOfMeasure
 * @property string itemUrl
 * @property integer companySystemID
 * @property string companyID
 * @property float maximunQty
 * @property float minimumQty
 * @property float rolQuantity
 * @property integer wacValueLocalCurrencyID
 * @property float wacValueLocal
 * @property integer wacValueReportingCurrencyID
 * @property float wacValueReporting
 * @property float totalQty
 * @property float totalValueLocal
 * @property float totalValueRpt
 * @property integer financeCategoryMaster
 * @property integer financeCategorySub
 * @property integer categorySub1
 * @property integer categorySub2
 * @property integer categorySub3
 * @property integer categorySub4
 * @property integer categorySub5
 * @property integer isActive
 * @property integer isAssigned
 * @property integer selectedForWarehouse
 * @property integer itemMovementCategory
 * @property string|\Carbon\Carbon timeStamp
 */
class ItemAssigned extends Model
{
    //use SoftDeletes;

    public $table = 'itemassigned';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'idItemAssigned';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'itemCodeSystem',
        'itemPrimaryCode',
        'secondaryItemCode',
        'barcode',
        'itemDescription',
        'itemUnitOfMeasure',
        'itemUrl',
        'companySystemID',
        'companyID',
        'maximunQty',
        'minimumQty',
        'rolQuantity',
        'wacValueLocalCurrencyID',
        'wacValueLocal',
        'wacValueReportingCurrencyID',
        'wacValueReporting',
        'totalQty',
        'totalValueLocal',
        'totalValueRpt',
        'financeCategoryMaster',
        'faFinanceCatID',
        'financeCategorySub',
        'categorySub1',
        'categorySub2',
        'categorySub3',
        'categorySub4',
        'categorySub5',
        'isActive',
        'isAssigned',
        'selectedForWarehouse',
        'itemMovementCategory',
        'timeStamp',
        'isPOSItem',
        'sellingCost',
        'roQuantity'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idItemAssigned' => 'integer',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'secondaryItemCode' => 'string',
        'barcode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'itemUrl' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'maximunQty' => 'float',
        'minimumQty' => 'float',
        'rolQuantity' => 'float',
        'wacValueLocalCurrencyID' => 'integer',
        'wacValueLocal' => 'float',
        'wacValueReportingCurrencyID' => 'integer',
        'wacValueReporting' => 'float',
        'totalQty' => 'float',
        'totalValueLocal' => 'float',
        'totalValueRpt' => 'float',
        'financeCategoryMaster' => 'integer',
        'faFinanceCatID' => 'integer',
        'financeCategorySub' => 'integer',
        'categorySub1' => 'integer',
        'categorySub2' => 'integer',
        'categorySub3' => 'integer',
        'categorySub4' => 'integer',
        'categorySub5' => 'integer',
        'isActive' => 'integer',
        'isAssigned' => 'integer',
        'selectedForWarehouse' => 'integer',
        'itemMovementCategory' => 'integer',
        'isPOSItem' => 'integer',
        'sellingCost' => 'float',
        'roQuantity' => 'float',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function purchase_request_details(){
        return $this->hasMany('App\Models\SupplierCurrency','supplierCodeSystem','currency');
    }

    public function unit(){
        return $this->hasOne('App\Models\Unit','UnitID','itemUnitOfMeasure');
    }

    public function financeMainCategory(){
        return $this->hasOne('App\Models\FinanceItemCategoryMaster','itemCategoryID','financeCategoryMaster');
    }

    public function financeSubCategory(){
        return $this->hasOne('App\Models\FinanceItemCategorySub','itemCategorySubID','financeCategorySub');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','itemCodeSystem');
    }

    public function local_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'wacValueLocalCurrencyID', 'currencyID');
    }

    public function rpt_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'wacValueReportingCurrencyID', 'currencyID');
    }

    public function outlet_items(){
        return $this->hasMany('App\Models\WarehouseItems','itemSystemCode','itemCodeSystem');
    }

    public function item_ledger(){
        return $this->hasMany('App\Models\ErpItemLedger','itemSystemCode','itemCodeSystem');
    }

    public function po_detail(){
        return $this->hasMany('App\Models\PurchaseOrderDetails','itemCode','itemCodeSystem');
    }

    public function grv_detail(){
        return $this->hasMany('App\Models\GRVDetails','itemCode','itemCodeSystem');
    }

    public function item_master(){
        return $this->belongsTo('App\Models\ItemMaster', 'itemCodeSystem', 'itemCodeSystem');
    }

    public function item_category_type(){
        return $this->hasMany('App\Models\ItemMasterCategoryType','itemCodeSystem','itemCodeSystem');
    }
}
