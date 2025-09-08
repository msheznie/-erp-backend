<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSGLEntries",
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
 *          property="documentSystemId",
 *          description="documentSystemId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
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
class POSGLEntries extends Model
{

    public $table = 'pos_gl_entries';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'shiftId',
        'invoiceID',
        'documentSystemId',
        'documentCode',
        'glCode',
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
        'documentSystemId' => 'integer',
        'documentCode' => 'string',
        'glCode' => 'integer',
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

     public function bankAccount() {
        return $this->belongsTo(BankAccount::class,'glCode','chartOfAccountSystemID')
            ->where('approvedYN', 1);
    }

    public function invoice() {
        return $this->belongsTo(POSInvoiceSource::class, 'invoiceID', 'invoiceID');
    } 

    
}
