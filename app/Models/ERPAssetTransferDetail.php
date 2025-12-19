<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ERPAssetTransferDetail",
 *      required={""},
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
class ERPAssetTransferDetail extends Model
{

    public $table = 'erp_fa_fa_asset_transfer_details';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'erp_fa_fa_asset_transfer_id',
        'erp_fa_fa_asset_request_id',
        'erp_fa_fa_asset_request_detail_id',
        'from_location_id',
        'to_location_id',
        'fa_master_id',
        'pr_created_yn',
        'company_id',
        'itemCodeSystem',
        'created_user_id',
        'from_emp_id',
        'to_emp_id',
        'departmentSystemID',
        'receivedYN'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'erp_fa_fa_asset_transfer_id' => 'integer',
        'erp_fa_fa_asset_request_id' => 'integer',
        'erp_fa_fa_asset_request_detail_id' => 'integer',
        'from_location_id' => 'integer',
        'to_location_id' => 'integer',
        'fa_master_id' => 'integer',
        'pr_created_yn' => 'integer',
        'company_id' => 'integer',
        'itemCodeSystem' => 'integer',
        'created_user_id' => 'integer',
        'from_emp_id' => 'integer',
        'to_emp_id' => 'integer',
        'departmentSystemID' => 'integer',
        'receivedYN' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function assetRequestDetail()
    {
        return $this->hasOne(AssetRequestDetail::class, 'id', 'erp_fa_fa_asset_request_detail_id');
    }

    public function item_detail()
    {
        return $this->belongsTo('App\Models\ItemMaster', 'itemCodeSystem', 'itemCodeSystem');
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
    public function assetTransferMaster()
    {
        return $this->hasOne(ERPAssetTransfer::class,'id','erp_fa_fa_asset_transfer_id');
    }
    public function smePayAsset()
    {
        return $this->hasOne(SMEPayAsset::class,'masterID','srp_erp_pay_assets_id');
    }
    public function fromEmployee()
    {
        return $this->hasOne(Employee::class,'employeeSystemID','from_emp_id');
    }
    public function toEmployee()
    {
        return $this->hasOne(Employee::class,'employeeSystemID','to_emp_id');
    }

    public function department()
    {
        return $this->hasOne(DepartmentMaster::class,'departmentSystemID','departmentSystemID');
    }
}
