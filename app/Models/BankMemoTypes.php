<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BankMemoTypes",
 *      required={""},
 *      @SWG\Property(
 *          property="bankMemoTypeID",
 *          description="bankMemoTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankMemoHeader",
 *          description="bankMemoHeader",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="sortOrder",
 *          description="sortOrder",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class BankMemoTypes extends Model
{

    public $table = 'erp_bankmemotypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey  = 'bankMemoTypeID';

    public $fillable = [
        'bankMemoHeader',
        'sortOrder'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bankMemoTypeID' => 'integer',
        'bankMemoHeader' => 'string',
        'sortOrder' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public static function getBankMemo()
    {
        return self::orderBy('sortOrder', 'asc')->get();
    }

    
}
