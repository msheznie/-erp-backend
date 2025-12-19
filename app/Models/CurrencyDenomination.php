<?php
/**
 * =============================================
 * -- File Name : CurrencyDenomination.php
 * -- Project Name : ERP
 * -- Module Name : Shift Details
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CurrencyDenomination",
 *      required={""},
 *      @SWG\Property(
 *          property="masterID",
 *          description="masterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currencyCode",
 *          description="currencyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="value",
 *          description="value",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="isNote",
 *          description="isNote",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="caption",
 *          description="caption",
 *          type="string"
 *      )
 * )
 */
class CurrencyDenomination extends Model
{

    public $table = 'erp_gpos_currencydenomination';
    
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    protected $primaryKey  = 'masterID';


    public $fillable = [
        'currencyID',
        'currencyCode',
        'amount',
        'value',
        'isNote',
        'caption'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'masterID' => 'integer',
        'currencyID' => 'integer',
        'currencyCode' => 'string',
        'amount' => 'float',
        'value' => 'float',
        'isNote' => 'boolean',
        'caption' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
