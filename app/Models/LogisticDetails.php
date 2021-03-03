<?php
/**
 * =============================================
 * -- File Name : LogisticDetails.php
 * -- Project Name : ERP
 * -- Module Name :  Logistic
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LogisticDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="logisticDetailsID",
 *          description="logisticDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="logisticMasterID",
 *          description="logisticMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="POid",
 *          description="POid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="POdetailID",
 *          description="POdetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemcodeSystem",
 *          description="itemcodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="partNo",
 *          description="partNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemUOM",
 *          description="itemUOM",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPOQtry",
 *          description="itemPOQtry",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="itemShippingQty",
 *          description="itemShippingQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="POdeliveryWarehousLocation",
 *          description="POdeliveryWarehousLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GRVStatus",
 *          description="GRVStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GRVsystemCode",
 *          description="GRVsystemCode",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class LogisticDetails extends Model
{

    public $table = 'erp_logisticdetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'logisticDetailsID';


    public $fillable = [
        'logisticMasterID',
        'companySystemID',
        'companyID',
        'supplierID',
        'POid',
        'POdetailID',
        'itemcodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'partNo',
        'itemUOM',
        'itemPOQtry',
        'itemShippingQty',
        'POdeliveryWarehousLocation',
        'GRVStatus',
        'GRVsystemCode',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'logisticDetailsID' => 'integer',
        'logisticMasterID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'supplierID' => 'integer',
        'POid' => 'integer',
        'POdetailID' => 'integer',
        'itemcodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'partNo' => 'string',
        'itemUOM' => 'integer',
        'itemPOQtry' => 'float',
        'itemShippingQty' => 'float',
        'POdeliveryWarehousLocation' => 'integer',
        'GRVStatus' => 'integer',
        'GRVsystemCode' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom(){
        return $this->belongsTo('App\Models\Unit','itemUOM','UnitID');
    }

    public function supplier_by()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierID', 'supplierCodeSystem');
    }

    public function warehouse_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','POdeliveryWarehousLocation','wareHouseSystemCode');
    }

    public function po()
    {
        return $this->belongsTo('App\Models\ProcumentOrder','POid','purchaseOrderID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\Logistic', 'logisticMasterID', 'logisticMasterID');
    }
}
