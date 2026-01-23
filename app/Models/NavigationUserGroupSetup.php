<?php
/**
 * =============================================
 * -- File Name : NavigationUserGroupSetup.php
 * -- Project Name : ERP
 * -- Module Name : Navigation User Group Setup
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class NavigationUserGroupSetup
 * @package App\Models
 * @version February 13, 2018, 9:01 am UTC
 *
 * @property integer userGroupID
 * @property string companyID
 * @property integer navigationMenuID
 * @property string description
 * @property integer masterID
 * @property string url
 * @property string pageID
 * @property string pageTitle
 * @property string pageIcon
 * @property integer levelNo
 * @property integer sortOrder
 * @property integer isSubExist
 * @property string|\Carbon\Carbon timestamp
 */
class NavigationUserGroupSetup extends Model
{
    //use SoftDeletes;

    public $table = 'srp_erp_navigationusergroupsetup';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    protected $appends = ['pageTitle'];


    public $fillable = [
        'userGroupID',
        'companyID',
        'navigationMenuID',
        'description',
        'masterID',
        'url',
        'pageID',
        'pageTitle',
        'pageIcon',
        'levelNo',
        'sortOrder',
        'isSubExist',
        'timestamp',
        'isPortalYN',
        'externalLink'
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'UserGroupSetupID' => 'integer',
        'userGroupID' => 'integer',
        'companyID' => 'integer',
        'navigationMenuID' => 'integer',
        'description' => 'string',
        'masterID' => 'integer',
        'url' => 'string',
        'pageID' => 'string',
        'pageTitle' => 'string',
        'pageIcon' => 'string',
        'levelNo' => 'integer',
        'sortOrder' => 'integer',
        'isSubExist' => 'integer',
        'isPortalYN' => 'integer',
        'externalLink' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function parent()
    {
        return $this->belongsTo('App\NavigationUserGroupSetup','UserGroupSetupID');
    }

    public function child()
    {
        return $this->hasMany(NavigationUserGroupSetup::class,'masterID','navigationMenuID');
    }

    public function language()
    {
        return $this->belongsTo(NavigationMenusLanguages::class,'navigationMenuID','navigationMenuID');

    }

    public function translations()
    {
        return $this->hasMany(NavigationMenusLanguages::class, 'navigationMenuID', 'navigationMenuID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getPageTitleAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->description) {
            return $translation->description;
        }

        return  $this->attributes['pageTitle'] ?? '';
    }

}
