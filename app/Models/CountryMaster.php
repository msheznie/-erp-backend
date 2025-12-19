<?php
/**
 * =============================================
 * -- File Name : CountryMaster.php
 * -- Project Name : ERP
 * -- Module Name : Country Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CountryMaster
 * @package App\Models
 * @version February 27, 2018, 11:30 am UTC
 *
 * @property string countryCode
 * @property string countryName
 * @property string countryName_O
 * @property string nationality
 * @property integer isLocal
 * @property string countryFlag
 */
class CountryMaster extends Model
{
    //use SoftDeletes;

    public $table = 'countrymaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'countryID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'countryCode',
        'countryName',
        'countryName_O',
        'nationality',
        'isLocal',
        'countryFlag'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'countryID' => 'integer',
        'countryCode' => 'string',
        'countryName' => 'string',
        'countryName_O' => 'string',
        'nationality' => 'string',
        'isLocal' => 'integer',
        'countryFlag' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['countryName'];

    /**
     * Get the translations for the country.
     */
    public function translations()
    {
        return $this->hasMany(CountryMasterTranslation::class, 'countryID', 'countryID');
    }

    /**
     * Get the translated country name based on current locale.
     *
     * @return string
     */
    public function getCountryNameAttribute()
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            $translation = $this->translations()->where('languageCode', 'ar')->first();
            return $translation ? $translation->countryName : $this->attributes['countryName'];
        }

        return $this->attributes['countryName'];
    }
    
}
