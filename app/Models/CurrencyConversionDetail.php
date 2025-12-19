<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CurrencyConversionDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="currencyConversionDetailAutoID",
 *          description="currencyConversionDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyConversioMasterID",
 *          description="currencyConversioMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterCurrencyID",
 *          description="masterCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subCurrencyID",
 *          description="subCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="conversion",
 *          description="conversion",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CurrencyConversionDetail extends Model
{

    public $table = 'currency_conversion_detail';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'currencyConversionDetailAutoID';


    public $fillable = [
        'currencyConversioMasterID',
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
        'currencyConversionDetailAutoID' => 'integer',
        'currencyConversioMasterID' => 'integer',
        'masterCurrencyID' => 'integer',
        'subCurrencyID' => 'integer',
        'conversion' => 'float',
        'timestamp' => 'datetime'
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
