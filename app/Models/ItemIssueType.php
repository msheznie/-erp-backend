<?php
/**
 * =============================================
 * -- File Name : ItemIssueType.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Type
 * -- Author : Mohamed Fayas
 * -- Create date : 20- June 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ItemIssueType",
 *      required={""},
 *      @SWG\Property(
 *          property="itemIssueTypeID",
 *          description="itemIssueTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="issueTypeDes",
 *          description="issueTypeDes",
 *          type="string"
 *      )
 * )
 */
class ItemIssueType extends Model
{

    public $table = 'erp_itemissuetype';
    
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    protected $primaryKey  = 'itemIssueTypeID';
    protected $appends = ['issueTypeDes'];


    public $fillable = [
        'issueTypeDes'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemIssueTypeID' => 'integer',
        'issueTypeDes' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Relationship to ItemIssueTypeLanguage
     */
    public function translations()
    {
        return $this->hasMany(ItemIssueTypeLanguage::class, 'itemIssueTypeID', 'itemIssueTypeID');
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
     * Get translated issue type description
     */
    public function getIssueTypeDesAttribute()
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation && $translation->issueTypeDes) {
            return $translation->issueTypeDes;
        }
        
        
        return $this->attributes['issueTypeDes'] ?? '';
    }
    
}
