<?php
/**
 * =============================================
 * -- File Name : SupplierCurrency.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Currency
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierCurrency
 * @package App\Models
 * @version March 2, 2018, 6:24 am UTC
 *
 * @property integer supplierCodeSystem
 * @property integer currencyID
 * @property string bankMemo
 * @property string|\Carbon\Carbon timestamp
 * @property integer isAssigned
 * @property integer isDefault
 */
class SupplierCurrency extends Model
{
   // use SoftDeletes;

    public $table = 'suppliercurrency';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'supplierCurrencyID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'supplierCodeSystem',
        'currencyID',
        'bankMemo',
        'timestamp',
        'isAssigned',
        'isDefault'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierCurrencyID' => 'integer',
        'supplierCodeSystem' => 'integer',
        'currencyID' => 'integer',
        'bankMemo' => 'string',
        'isAssigned' => 'integer',
        'isDefault' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function currencyMaster()
    {
        return $this->belongsTo('App\Models\CurrencyMaster','currencyID','currencyID');
    }

    public function bankMemo_by(){
        return $this->hasMany('App\Models\BankMemoSupplier','supplierCurrencyID','supplierCurrencyID');
    }

    public static function getCurrency($selectedSupplier,$currency)
    {
        return self::where('supplierCodeSystem',$selectedSupplier)
            ->where('currencyID',$currency)
            ->first();
    }
    
}
