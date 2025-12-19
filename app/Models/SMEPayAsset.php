<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SMEPayAsset",
 *      required={""},
 *      @SWG\Property(
 *          property="masterID",
 *          description="masterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetTypeID",
 *          description="FK => srp_erp_pay_assettype.id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="asset_serial_no",
 *          description="asset_serial_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetConditionID",
 *          description="FK => srp_erp_pay_assetcondition.id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="handOverDate",
 *          description="handOverDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="returnStatus",
 *          description="returnStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="returnDate",
 *          description="returnDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="returnComment",
 *          description="returnComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
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
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SMEPayAsset extends Model
{

    public $table = 'srp_erp_pay_assets';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public $timestamps = false;


    public $fillable = [
        'empID',
        'assetTypeID',
        'description',
        'asset_serial_no',
        'assetConditionID',
        'handOverDate',
        'returnStatus',
        'returnDate',
        'returnComment',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'timestamp',
        'Erp_faID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'masterID' => 'integer',
        'empID' => 'integer',
        'assetTypeID' => 'integer',
        'description' => 'string',
        'asset_serial_no' => 'string',
        'assetConditionID' => 'integer',
        'handOverDate' => 'date',
        'returnStatus' => 'integer',
        'returnDate' => 'date',
        'returnComment' => 'string',
        'companyID' => 'integer',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'integer',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'integer',
        'modifiedDateTime' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
