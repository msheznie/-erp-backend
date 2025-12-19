<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RegisteredSupplierCurrency",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="registeredSupplierID",
 *          description="registeredSupplierID",
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
 *          property="isAssigned",
 *          description="isAssigned",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class RegisteredSupplierCurrency extends Model
{

    public $table = 'registeredsuppliercurrency';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'id';
    public $timestamps = false;


    public $fillable = [
        'registeredSupplierID',
        'currencyID',
        'isAssigned',
        'isDefault'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'registeredSupplierID' => 'integer',
        'currencyID' => 'integer',
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

     public function currency_master()
    {
        return $this->belongsTo('App\Models\CurrencyMaster','currencyID','currencyID');
    }
}
