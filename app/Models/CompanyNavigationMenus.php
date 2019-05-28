<?php
/**
=============================================
-- File Name : CompanyNavigationMenus.php
-- Project Name : ERP
-- Module Name :  Navigation
-- Author : Mubashir
-- Create date : 14 - March 2018
-- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CompanyNavigationMenus
 * @package App\Models
 * @version March 15, 2018, 7:59 am UTC
 *
 * @property string description
 * @property integer companyID
 * @property integer masterID
 * @property integer languageID
 * @property string url
 * @property string pageID
 * @property string pageTitle
 * @property string pageIcon
 * @property integer levelNo
 * @property integer sortOrder
 * @property integer isSubExist
 * @property string|\Carbon\Carbon timestamp
 * @property integer isAddon
 * @property string addonDescription
 * @property string addonDetails
 * @property integer isCoreModule
 * @property integer isGroup
 */
class CompanyNavigationMenus extends Model
{
    //use SoftDeletes;

    public $table = 'srp_erp_companynavigationmenus';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'description',
        'companyID',
        'masterID',
        'languageID',
        'url',
        'pageID',
        'pageTitle',
        'pageIcon',
        'levelNo',
        'sortOrder',
        'isSubExist',
        'timestamp',
        'isAddon',
        'addonDescription',
        'addonDetails',
        'isCoreModule',
        'isGroup',
        'isPortalYN',
        'externalLink'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'navigationMenuID' => 'integer',
        'description' => 'string',
        'companyID' => 'integer',
        'masterID' => 'integer',
        'languageID' => 'integer',
        'url' => 'string',
        'pageID' => 'string',
        'pageTitle' => 'string',
        'pageIcon' => 'string',
        'levelNo' => 'integer',
        'sortOrder' => 'integer',
        'isSubExist' => 'integer',
        'isAddon' => 'integer',
        'addonDescription' => 'string',
        'addonDetails' => 'string',
        'isCoreModule' => 'integer',
        'isGroup' => 'integer',
        'isPortalYN' => 'integer',
        'externalLink' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
