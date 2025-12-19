<?php
/**
 * =============================================
 * -- File Name : AddressType.php
 * -- Project Name : ERP
 * -- Module Name :  Address Type
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AddressType",
 *      required={""},
 *      @SWG\Property(
 *          property="addressTypeID",
 *          description="addressTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addressTypeDescription",
 *          description="addressTypeDescription",
 *          type="string"
 *      )
 * )
 */
class AddressType extends Model
{

    public $table = 'erp_addresstype';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['addressTypeDescription'];

    protected $primaryKey = 'addressTypeID';


    public $fillable = [
        'addressTypeDescription',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'addressTypeID' => 'integer',
        'addressTypeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Get the translations for the address type.
     */
    public function translations()
    {
        return $this->hasMany(AddressTypeTranslation::class, 'addressTypeID', 'addressTypeID');
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
     * Get the translated addressTypeDescription attribute.
     */
    public function getAddressTypeDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->addressTypeDescription;
        }
        
        return $this->attributes['addressTypeDescription'] ?? '';
    }

}
