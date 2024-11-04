<?php
/**
=============================================
-- File Name : WarehouseMaster.php
-- Project Name : ERP
-- Module Name :  System Admin
-- Author : Pasan Madhuranga
-- Create date : 22 - March 2018
-- Description : This file is used to interact with database table and it contains relationships to the tables.
-- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ChartOfAccount;

/**
 * Class WarehouseMaster
 * @package App\Models
 * @version March 15, 2018, 7:30 am UTC
 *
 * @property string wareHouseCode
 * @property string wareHouseDescription
 * @property integer wareHouseLocation
 * @property integer isActive
 * @property string companyID
 * @property integer companySystemID
 * @property string|\Carbon\Carbon timestamp
 */
class WarehouseMaster extends Model
{
    //use SoftDeletes;

    public $table           = 'warehousemaster';
    const CREATED_AT        = 'createdDateTime';
    const UPDATED_AT        = 'timeStamp';
    protected $primaryKey   = 'wareHouseSystemCode';
    protected $dates        = ['deleted_at'];


    public $fillable = [
        'wareHouseCode',
        'wareHouseDescription',
        'wareHouseLocation',
        'isActive',
        'companyID',
        'companySystemID',
        'timestamp',
        'isDefault',
        'isPosLocation',
        'posFooterNote',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserSystemID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'templateImgUrl',
        'printTemplateId',
         'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */

    protected $casts = [
        'wareHouseSystemCode' => 'integer',
        'wareHouseCode' => 'string',
        'wareHouseDescription' => 'string',
        'wareHouseLocation' => 'integer',
        'isActive' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'isDefault' => 'integer',
        'isPosLocation' => 'integer',
        'posFooterNote' => 'string',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'createdDateTime' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'string',
        'modifiedUserName' => 'string',
        'templateImgUrl' => 'string',
        'printTemplateId' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\ErpLocation', 'wareHouseLocation', 'locationID');
    }

    public function bin_locations()
    {
        return $this->hasMany(WarehouseBinLocation::class, 'wareHouseSystemCode');
    }

    public function sub_levels(){
        return $this->hasMany(WarehouseSubLevels::class, 'warehouse_id');
    }

    public static function checkManuefactoringWareHouse($wareHouseSystemCode)
    {
        $wareHouse = WarehouseMaster::find($wareHouseSystemCode);

        if ($wareHouse) {
            return ($wareHouse->manufacturingYN == 1) ? true : false;
        } else {
            return false;
        }
    } 

    public static function getWIPGLSystemID($wareHouseSystemCode)
    {
        $wareHouse = WarehouseMaster::find($wareHouseSystemCode);

        if ($wareHouse) {
            return $wareHouse->WIPGLCode;
        } else {
            return null;
        }
    }


    public static function getWIPGLCode($wareHouseSystemCode)
    {
        $wareHouse = WarehouseMaster::find($wareHouseSystemCode);

        if ($wareHouse) {
            $chartOfAccount = ChartOfAccount::find($wareHouse->WIPGLCode);
            return (isset($chartOfAccount->AccountCode) ? $chartOfAccount->AccountCode : null);
        } else {
            return null;
        }
    }

}
