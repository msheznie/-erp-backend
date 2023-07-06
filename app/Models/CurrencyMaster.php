<?php
/**
 * =============================================
 * -- File Name : CurrencyMaster.php
 * -- Project Name : ERP
 * -- Module Name : Currency Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CurrencyMaster
 * @package App\Models
 * @version February 26, 2018, 6:14 am UTC
 *
 * @property string CurrencyName
 * @property string CurrencyCode
 * @property integer DecimalPlaces
 * @property float ExchangeRate
 * @property integer isLocal
 * @property string|\Carbon\Carbon DateModified
 * @property string ModifiedBy
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class CurrencyMaster extends Model
{
    //use SoftDeletes;

    public $table = 'currencymaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'currencyID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'CurrencyName',
        'CurrencyCode',
        'DecimalPlaces',
        'ExchangeRate',
        'isLocal',
        'DateModified',
        'ModifiedBy',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'currencyID' => 'integer',
        'CurrencyName' => 'string',
        'CurrencyCode' => 'string',
        'DecimalPlaces' => 'integer',
        'ExchangeRate' => 'float',
        'isLocal' => 'integer',
        'ModifiedBy' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function customerCurrencies()
    {
        return $this->hasMany('App\Models\CustomerCurrency', 'currencyID','currencyID');
    }


    public static function getCurrencyCode($currencyID)
    {
        $currency = CurrencyMaster::find($currencyID);

        return $currency ? $currency->CurrencyCode : "";
    }

     public static function getDecimalPlaces($currencyID)
    {
        $currency = CurrencyMaster::find($currencyID);

        return $currency ? $currency->DecimalPlaces : 2;
    }
}
