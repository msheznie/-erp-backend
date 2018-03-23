<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class NavigationMenus
 * @package App\Models
 * @version February 13, 2018, 9:00 am UTC
 *
 * @property string description
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
class NavigationMenus extends Model
{
    //use SoftDeletes;

    public $table = 'srp_erp_navigationmenus';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'description',
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
        'isGroup'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'navigationMenuID' => 'integer',
        'description' => 'string',
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
        'isGroup' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function child()
    {
        return $this->hasMany(CompanyNavigationMenus::class,'masterID','navigationMenuID');
    }

    
}
