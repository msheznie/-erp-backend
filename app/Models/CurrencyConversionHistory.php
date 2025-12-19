<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CurrencyConversionHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="conversionhistoryID",
 *          description="conversionhistoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CurrencyConversionHistory extends Model
{

    public $table = 'currencyconversionhistory';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'conversionhistoryID';


    public $fillable = [
        'serialNo',
        'masterCurrencyID',
        'subCurrencyID',
        'conversion',
        'createdBy',
        'createdUserID',
        'createdpc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'conversionhistoryID' => 'integer',
        'serialNo' => 'integer',
        'masterCurrencyID' => 'integer',
        'subCurrencyID' => 'integer',
        'conversion' => 'float',
        'createdBy' => 'string',
        'createdUserID' => 'integer',
        'createdpc' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'conversionhistoryID' => 'required'
    ];

     public function sub_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'subCurrencyID','currencyID');
    }
    
}
