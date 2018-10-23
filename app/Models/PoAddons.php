<?php
/**
 * =============================================
 * -- File Name : PoAddons.php
 * -- Project Name : ERP
 * -- Module Name :  PoAddons
 * -- Author : Mohamed Nazir
 * -- Create date : 20 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PoAddons",
 *      required={""},
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
 *      )
 * )
 */
class PoAddons extends Model
{

    public $table = 'erp_poaddons';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'idpoAddons';

    public $fillable = [
        'poId',
        'idaddOnCostCategories',
        'supplierID',
        'currencyID',
        'amount',
        'glCode',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'idpoAddons' => 'integer',
        'poId' => 'integer',
        'idaddOnCostCategories' => 'integer',
        'supplierID' => 'integer',
        'currencyID' => 'integer',
        'amount' => 'float',
        'glCode' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\AddonCostCategories', 'idaddOnCostCategories', 'idaddOnCostCategories');
    }

    
}
