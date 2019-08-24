<?php
/**
 * =============================================
 * -- File Name : CurrencyConversion.php
 * -- Project Name : ERP
 * -- Module Name :  Finance
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CurrencyConversion
 * @package App\Models
 * @version March 30, 2018, 9:09 am UTC
 *
 * @property integer masterCurrencyID
 * @property integer subCurrencyID
 * @property float conversion
 * @property string|\Carbon\Carbon timestamp
 */
class CurrencyConversion extends Model
{
    //use SoftDeletes;

    public $table = 'currencyconversion';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'currencyConversionAutoID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'masterCurrencyID',
        'subCurrencyID',
        'conversion',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'currencyConversionAutoID' => 'integer',
        'masterCurrencyID' => 'integer',
        'subCurrencyID' => 'integer',
        'conversion' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function sub_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'subCurrencyID','currencyID');
    }

}
