<?php
/**
 * =============================================
 * -- File Name : DocumentRestrictionPolicy.php
 * -- Project Name : ERP
 * -- Module Name :  Document Restriction Policy
 * -- Author : Fayas
 * -- Create date : 14 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentRestrictionPolicy",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="policyDescription",
 *          description="policyDescription",
 *          type="string"
 *      )
 * )
 */
class DocumentRestrictionPolicy extends Model
{
    public $table = 'documentrestrictionpolicy';
    
    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;

    protected $appends = ['policy_description_translated'];

    public $fillable = [
        'documentSystemID',
        'documentID',
        'policyDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'policyDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function assign(){
        return $this->hasMany('App\Models\DocumentRestrictionAssign','documentRestrictionPolicyID');
    }

    public function translations()
    {
        return $this->hasMany(DocumentRestrictionPolicyTranslation::class, 'policyId', 'id');
    }

    public function translation($languageCode = null)
    {
        if (!$languageCode) {
            $languageCode = app()->getLocale() ?: 'en';
        }
        
        return $this->translations()->where('languageCode', $languageCode)->first();
    }

    public function getPolicyDescriptionTranslatedAttribute($value)
    {
        $currentLanguage = app()->getLocale() ?: 'en';
        
        $translation = $this->translation($currentLanguage);
        
        if ($translation) {
            return $translation->description;
        }
        
        return $value;
    }
}
