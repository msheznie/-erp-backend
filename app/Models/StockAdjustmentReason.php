<?php
/**
=============================================
-- File Name : StockAdjustmentReason.php
-- Project Name : ERP
-- Module Name :  System Admin
-- Author : Saravanan
-- Create date : 11 - March 2022
-- Description : This file is used to interact with database table and it contains relationships to the tables.
-- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Unit
 * @package App\Models
 * @version March 22, 2018, 6:41 am UTC
 *
 * @property string reason
 * @property string is_active
 */
class StockAdjustmentReason extends Model
{
    //use SoftDeletes;

    public $table = 'stockadjustment_reasons';
    
    protected $primaryKey  = 'id';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['reason'];

    public $fillable = [
        'reason',
        'is_active	',
        'timeStamp'
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to StockAdjustmentReasonLanguage
     */
    public function translations()
    {
        return $this->hasMany(StockAdjustmentReasonLanguage::class, 'reasonID', 'id');
    }

    /**
     * Get translation for specific language
     */
    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    /**
     * Get translated reason description
     */
    public function getReasonAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->reasonDescription;
        }
        
        if ($currentLanguage !== 'en') {
            $englishTranslation = $this->translation('en');
            if ($englishTranslation) {
                return $englishTranslation->reasonDescription;
            }
        }
        
        return $this->reason;
    }
 
}
