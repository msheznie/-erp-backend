<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSBankGLEntries",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shiftId",
 *          description="shiftId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankAccId",
 *          description="bankAccId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logId",
 *          description="logId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isReturnYN",
 *          description="isReturnYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      )
 * )
 */
class POSBankGLEntries extends Model
{

    public $table = 'pos_bank_gl';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'shiftId',
        'invoiceID',
        'bankAccId',
        'logId',
        'isReturnYN',
        'amount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'shiftId' => 'integer',
        'bankAccId' => 'integer',
        'logId' => 'integer',
        'isReturnYN' => 'integer',
        'amount' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

   
}
