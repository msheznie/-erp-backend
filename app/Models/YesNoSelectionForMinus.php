<?php
/**
 * =============================================
 * -- File Name : YesNoSelectionForMinus.php
 * -- Project Name : ERP
 * -- Module Name :  Yes No Selection For Minus
 * -- Author : Mohamed Fayas
 * -- Create date : 26 - March 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class YesNoSelectionForMinus
 * @package App\Models
 * @version March 27, 2018, 7:38 am UTC
 *
 * @property string selection
 */
class YesNoSelectionForMinus extends Model
{
    //use SoftDeletes;

    public $table = 'yesnoselectionforminus';

    protected $appends = ['yes_no_label_minus'];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'selection'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'ID' => 'integer',
        'selection' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function translations()
    {
        return $this->hasMany(YesNoMinusSelectionLanguage::class, 'yesNoSelectionID', 'ID');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

     public function getYesNoLabelMinusAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->YesNo;
        }
        
        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->YesNo;
            }
        }
        
        return $value;
    }

    
}
