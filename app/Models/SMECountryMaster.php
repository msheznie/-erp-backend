<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMECountryMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="countryShortCode",
 *          description="countryShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CountryDes",
 *          description="CountryDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Nationality",
 *          description="Nationality",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryCode",
 *          description="countryCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="countryTimeZone",
 *          description="countryTimeZone",
 *          type="string"
 *      )
 * )
 */
class SMECountryMaster extends Model
{

    public $table = 'srp_erp_countrymaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'countryShortCode',
        'CountryDes',
        'Nationality',
        'countryCode',
        'countryTimeZone'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'countryID' => 'integer',
        'countryShortCode' => 'string',
        'CountryDes' => 'string',
        'Nationality' => 'string',
        'countryCode' => 'integer',
        'countryTimeZone' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'countryShortCode' => 'required',
        'CountryDes' => 'required'
    ];

    
}
