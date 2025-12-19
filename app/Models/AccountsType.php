<?php
/**
 * =============================================
 * -- File Name : AccountsType.php
 * -- Project Name : ERP
 * -- Module Name :  Accounts Type
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AccountsType
 * @package App\Models
 * @version March 16, 2018, 8:44 am UTC
 *
 * @property string description
 * @property string code
 */
class AccountsType extends Model
{
    //use SoftDeletes;

    public $table = 'accountstype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey  = 'accountsType';
    protected $appends = ['description'];

    protected $dates = ['deleted_at'];


    public $fillable = [
        'description',
        'code'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'accountsType' => 'integer',
        'description' => 'string',
        'code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Get the translations for the accounts type.
     */
    public function translations()
    {
        return $this->hasMany(AccountsTypeTranslation::class, 'accountsType', 'accountsType');
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
