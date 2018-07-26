<?php
/**
 * =============================================
 * -- File Name : PoAddonsRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  PoAddonsRefferedBack
 * -- Author : Nazir
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PoAddonsRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="POAddonRefferedBackID",
 *          description="POAddonRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="idpoAddons",
 *          description="idpoAddons",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poId",
 *          description="poId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="idaddOnCostCategories",
 *          description="idaddOnCostCategories",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
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
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class PoAddonsRefferedBack extends Model
{

    public $table = 'erp_poaddonsrefferedback';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'POAddonRefferedBackID';



    public $fillable = [
        'idpoAddons',
        'poId',
        'idaddOnCostCategories',
        'supplierID',
        'currencyID',
        'amount',
        'glCode',
        'timesReferred',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'POAddonRefferedBackID' => 'integer',
        'idpoAddons' => 'integer',
        'poId' => 'integer',
        'idaddOnCostCategories' => 'integer',
        'supplierID' => 'integer',
        'currencyID' => 'integer',
        'amount' => 'float',
        'glCode' => 'string',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
