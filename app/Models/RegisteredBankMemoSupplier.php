<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="RegisteredBankMemoSupplier",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="memoHeader",
 *          description="memoHeader",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="memoDetail",
 *          description="memoDetail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="registeredSupplierID",
 *          description="registeredSupplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierCurrencyID",
 *          description="supplierCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankMemoTypeID",
 *          description="bankMemoTypeID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class RegisteredBankMemoSupplier extends Model
{

    public $table = 'registeredbankmemosupplier';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'id';
    public $timestamps = false;


    public $fillable = [
        'memoHeader',
        'memoDetail',
        'registeredSupplierID',
        'supplierCurrencyID',
        'bankMemoTypeID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'memoHeader' => 'string',
        'memoDetail' => 'string',
        'registeredSupplierID' => 'integer',
        'supplierCurrencyID' => 'integer',
        'bankMemoTypeID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
