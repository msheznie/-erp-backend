<?php
/**
 * =============================================
 * -- File Name : WarehouseBinLocation.php
 * -- Project Name : ERP
 * -- Module Name :  Warehouse Bin Location
 * -- Author : Mohamed Fayas
 * -- Create date : 07- September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="WarehouseBinLocation",
 *      required={""},
 *      @SWG\Property(
 *          property="binLocationID",
 *          description="binLocationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="binLocationDes",
 *          description="binLocationDes",
 *          type="string"
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
 *          property="wareHouseSystemCode",
 *          description="wareHouseSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdBy",
 *          description="createdBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class WarehouseBinLocation extends Model
{

    public $table = 'warehousebinlocationmaster';
    
    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'binLocationID';

    public $fillable = [
        'binLocationDes',
        'companySystemID',
        'companyID',
        'wareHouseSystemCode',
        'createdBy',
        'dateCreated',
        'isActive',
        'timeStamp',
        'warehouseSubLevelId',
        'isDeleted',
        'deleted_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'binLocationID' => 'integer',
        'binLocationDes' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'wareHouseSystemCode' => 'integer',
        'createdBy' => 'string',
        'isActive' => 'integer',
        'isDeleted' => 'integer',
        'warehouseSubLevelId' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function warehouse_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseSystemCode','wareHouseSystemCode');
    }

    public function sub_level()
    {
        return $this->belongsTo(WarehouseSubLevels::class,'warehouseSubLevelId');
    }
}
