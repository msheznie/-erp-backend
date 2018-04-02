<?php

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
        'timeStamp'
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
        'financeCategorySub' => 'integer',
        'categorySub1' => 'integer',
        'categorySub2' => 'integer',
        'categorySub3' => 'integer',
        'categorySub4' => 'integer',
        'categorySub5' => 'integer',
        'isActive' => 'integer',
        'isAssigned' => 'integer',
        'selectedForWarehouse' => 'integer',
        'itemMovementCategory' => 'integer'
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

}
