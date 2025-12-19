<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ExpenseAssetAllocation",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetID",
 *          description="assetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
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
 *          property="amount",
 *          description="amount",
 *          type="number",
 *          format="number"
 *      )
 * )
 */
class ExpenseAssetAllocation extends Model
{

    public $table = 'expense_asset_allocation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'assetID',
        'documentSystemID',
        'documentDetailID',
        'chartOfAccountSystemID',
        'documentSystemCode',
        'amountRpt',
        'amountLocal',
        'amount',
        'allocation_qty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'assetID' => 'integer',
        'documentSystemID' => 'integer',
        'documentDetailID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'documentSystemCode' => 'integer',
        'amount' => 'float',
        'allocation_qty' => 'float',
        'amountRpt' => 'float',
        'amountLocal' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

     public function asset()
    {
        return $this->belongsTo('App\Models\FixedAssetMaster', 'assetID','faID');
    } 

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function supplier_invoice()
    {
       return $this->belongsTo('App\Models\BookInvSuppMaster', 'documentSystemCode', 'bookingSuppMasInvAutoID');
    }

    public function payment_voucher()
    {
       return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'documentSystemCode', 'PayMasterAutoId');
    }

    public function meterial_issue()
    {
       return $this->belongsTo('App\Models\ItemIssueDetails', 'documentSystemCode', 'itemIssueAutoID');
    }

    public function journal_voucher()
    {
       return $this->belongsTo('App\Models\JvMaster', 'documentSystemCode', 'jvMasterAutoId');
    }

    public function grv()
    {
       return $this->belongsTo('App\Models\GRVMaster', 'documentSystemCode', 'grvAutoID');
    }

    public function ioue()
    {
       return $this->belongsTo('App\Models\IOUBookingMaster', 'documentSystemCode', 'bookingMasterID');
    }



    public function document()
    {
       return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID', 'documentSystemID');
    }
}
