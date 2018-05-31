<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderStatus.php
 * -- Project Name : ERP
 * -- Module Name : PurchaseOrderStatus
 * -- Author : Mohamed Fayas
 * -- Create date : 30- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PurchaseOrderStatus",
 *      required={""},
 *      @SWG\Property(
 *          property="POStatusID",
 *          description="POStatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderID",
 *          description="purchaseOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderCode",
 *          description="purchaseOrderCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="POCategoryID",
 *          description="POCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="updatedByEmpSystemID",
 *          description="updatedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updatedByEmpID",
 *          description="updatedByEmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updatedByEmpName",
 *          description="updatedByEmpName",
 *          type="string"
 *      )
 * )
 */
class PurchaseOrderStatus extends Model
{

    public $table = 'purchaseorderstatus';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'updatedDate';
    protected $primaryKey  = 'POStatusID';



    public $fillable = [
        'purchaseOrderID',
        'purchaseOrderCode',
        'POCategoryID',
        'comments',
        'updatedByEmpSystemID',
        'updatedByEmpID',
        'updatedByEmpName',
        'updatedDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'POStatusID' => 'integer',
        'purchaseOrderID' => 'integer',
        'purchaseOrderCode' => 'string',
        'POCategoryID' => 'integer',
        'comments' => 'string',
        'updatedByEmpSystemID' => 'integer',
        'updatedByEmpID' => 'integer',
        'updatedByEmpName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function category()
    {
        return $this->belongsTo('App\Models\PurchaseOrderCategory','POCategoryID','POCategoryID');
    }

    
}
