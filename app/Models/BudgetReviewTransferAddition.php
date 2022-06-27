<?php

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="BudgetReviewTransferAddition",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetTransferAdditionID",
 *          description="budgetTransferAdditionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="budgetTransferType",
 *          description="budgetTransferType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class BudgetReviewTransferAddition extends Model
{
    use Compoships;
    public $table = 'budget_review_transfer_addition';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'id';


    public $fillable = [
        'budgetTransferAdditionID',
        'budgetTransferType',
        'documentSystemCode',
        'documentSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'budgetTransferAdditionID' => 'integer',
        'budgetTransferType' => 'integer',
        'documentSystemCode' => 'integer',
        'documentSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function purchase_order()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'documentSystemCode', 'purchaseOrderID')
                    ->where('documentSystemID', 2);
    }

    public function purchase_request()
    {
        return $this->belongsTo('App\Models\PurchaseRequest', 'documentSystemCode', 'purchaseRequestID')
                    ->where('documentSystemID', 1);
    }
}
