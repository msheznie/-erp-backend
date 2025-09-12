<?php
/**
 * =============================================
 * -- File Name : ControlAccount.php
 * -- Project Name : ERP
 * -- Module Name : Control Account
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ControlAccount
 * @package App\Models
 * @version March 16, 2018, 4:52 am UTC
 *
 * @property string controlAccountCode
 * @property string description
 * @property string itemLedgerShymbol
 * @property string|\Carbon\Carbon timeStamp
 */


class ControlAccount extends Model
{
    //use SoftDeletes;

    public $table = 'controlaccounts';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'supplierCodeSystem';
    protected $appends = ['description'];

    protected $dates = ['timeStamp'];


    public $fillable = [
        'controlAccountCode',
        'description',
        'itemLedgerShymbol',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'controlAccountsSystemID' => 'integer',
        'controlAccountsID' => 'string',
        'controlAccountCode' => 'string',
        'description' => 'string',
        'itemLedgerShymbol' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Get the translations for the control account.
     */
    public function translations()
    {
        return $this->hasMany(ControlAccountTranslation::class, 'controlAccountsSystemID', 'controlAccountsSystemID');
    }

    /**
     * Get the translation for a specific language.
     */
    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    /**
     * Get the translated description attribute.
     */
    public function getDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->description;
        }
        
        return $this->attributes['description'] ?? '';
    }

}
