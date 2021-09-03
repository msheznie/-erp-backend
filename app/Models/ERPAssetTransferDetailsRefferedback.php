<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ERPAssetTransferDetailsRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="assetTransferDetailsRefferedBackID",
 *          description="assetTransferDetailsRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="erp_fa_fa_asset_transfer_id",
 *          description="erp_fa_fa_asset_transfer_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="erp_fa_fa_asset_request_id",
 *          description="erp_fa_fa_asset_request_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="erp_fa_fa_asset_request_detail_id",
 *          description="erp_fa_fa_asset_request_detail_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="from_location_id",
 *          description="from_location_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="to_location_id",
 *          description="to_location_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="receivedYN",
 *          description="receivedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fa_master_id",
 *          description="fa_master_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pr_created_yn",
 *          description="pr_created_yn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_user_id",
 *          description="created_user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class ERPAssetTransferDetailsRefferedback extends Model
{

    public $table = 'erp_assettransferdetailsrefferedback';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'id',
        'erp_fa_fa_asset_transfer_id',
        'erp_fa_fa_asset_request_id',
        'erp_fa_fa_asset_request_detail_id',
        'from_location_id',
        'to_location_id',
        'receivedYN',
        'fa_master_id',
        'pr_created_yn',
        'timesReferred',
        'company_id',
        'created_user_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'assetTransferDetailsRefferedBackID' => 'integer',
        'id' => 'integer',
        'erp_fa_fa_asset_transfer_id' => 'integer',
        'erp_fa_fa_asset_request_id' => 'integer',
        'erp_fa_fa_asset_request_detail_id' => 'integer',
        'from_location_id' => 'integer',
        'to_location_id' => 'integer',
        'receivedYN' => 'integer',
        'fa_master_id' => 'integer',
        'pr_created_yn' => 'integer',
        'timesReferred' => 'integer',
        'company_id' => 'integer',
        'created_user_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required'
    ];

    public function assetRequestDetail()
    {
        return $this->hasOne(AssetRequestDetail::class, 'id', 'erp_fa_fa_asset_request_detail_id');
    }

    public function assetMaster(){ 
        return $this->hasOne(FixedAssetMaster::class, 'faID', 'fa_master_id');
    }

    public function fromLocation()
    {
        return $this->hasOne(ErpLocation::class,'locationID','from_location_id');
    }
    public function toLocation()
    {
        return $this->hasOne(ErpLocation::class,'locationID','to_location_id');
    }
    public function confirmed_by()
    {
        return $this->hasOne(SrpEmployeeDetails::class,'EIdNo','confirmed_by_emp_id');
    }
    public function assetRequestMaster()
    {
        return $this->hasOne(AssetRequest::class,'id','erp_fa_fa_asset_request_id');
    }

    
}
