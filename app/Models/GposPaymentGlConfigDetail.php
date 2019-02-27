<?php
/**
 * =============================================
 * -- File Name : GposPaymentGlConfigDetail.php
 * -- Project Name : ERP
 * -- Module Name :  General pos Payment Gl Config Detail
 * -- Author : Fayas
 * -- Create date : 08 - January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GposPaymentGlConfigDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="ID",
 *          description="ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="paymentConfigMasterID",
 *          description="paymentConfigMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="GLCode",
 *          description="GLCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="warehouseID",
 *          description="warehouseID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAuthRequired",
 *          description="isAuthRequired",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      )
 * )
 */
class GposPaymentGlConfigDetail extends Model
{

    public $table = 'erp_gpos_paymentglconfigdetail';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'ID';



    public $fillable = [
        'paymentConfigMasterID',
        'GLCode',
        'companyID',
        'companyCode',
        'warehouseID',
        'isAuthRequired',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ID' => 'integer',
        'paymentConfigMasterID' => 'integer',
        'GLCode' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'warehouseID' => 'integer',
        'isAuthRequired' => 'boolean',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','warehouseID','wareHouseSystemCode');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'GLCode','chartOfAccountSystemID');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\GposPaymentGlConfigMaster', 'paymentConfigMasterID','autoID');
    }
}
