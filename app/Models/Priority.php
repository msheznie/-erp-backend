<?php
/**
 * =============================================
 * -- File Name : Priority.php
 * -- Project Name : ERP
 * -- Module Name : Priority
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Priority
 * @package App\Models
 * @version March 26, 2018, 10:51 am UTC
 *
 * @property string priorityDescription
 */
class Priority extends Model
{
    //use SoftDeletes;

    public $table = 'erp_priority';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $appends = ['priorityDescription'];

    protected $dates = ['deleted_at'];


    public $fillable = [
        'priorityDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'priorityID' => 'integer',
        'priorityDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to PriorityLanguage
     */
    public function translations()
    {
        return $this->hasMany(PriorityLanguage::class, 'priorityID', 'priorityID');
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
     * Get translated priority description
     */
    public function getPriorityDescriptionAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->priorityDescription) {
            return $translation->priorityDescription;
        }
        
        
        return $this->attributes['priorityDescription'] ?? '';
    }
    
}
