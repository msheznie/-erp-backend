<?php
/**
 * =============================================
 * -- File Name : DocumentRestrictionPolicyTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Document Restriction Policy Translation
 * -- Author : System
 * -- Create date : 14 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentRestrictionPolicyTranslation",
 *      required={"policyId", "languageCode", "description"},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="policyId",
 *          description="policyId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageCode",
 *          description="languageCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      )
 * )
 */
class DocumentRestrictionPolicyTranslation extends Model
{
    public $table = 'documentrestrictionpolicy_translations';
    
    public $timestamps = true;

    public $fillable = [
        'policyId',
        'languageCode',
        'description'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'policyId' => 'integer',
        'languageCode' => 'string',
        'description' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'policyId' => 'required|integer|exists:documentrestrictionpolicy,id',
        'languageCode' => 'required|string|max:10',
        'description' => 'required|string|max:255'
    ];

    /**
     * Get the policy that owns the translation.
     */
    public function policy()
    {
        return $this->belongsTo(DocumentRestrictionPolicy::class, 'policyId', 'id');
    }
}
