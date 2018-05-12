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
    const CREATED_AT        = 'timeStamp';
    const UPDATED_AT        = 'timeStamp';
    protected $primaryKey   = 'wareHouseSystemCode';
    protected $dates        = ['deleted_at'];
    //protected $timestamp = false;
    //public $timestamps = false;

    public $fillable = [
        'wareHouseCode',
        'wareHouseDescription',
        'wareHouseLocation',
        'isActive',
        'companyID',
        'companySystemID',
        'timestamp'
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
        'companySystemID' => 'integer'
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

}
