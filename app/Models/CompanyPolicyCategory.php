<?php
/**
 * =============================================
 * -- File Name : CompanyPolicyCategory.php
 * -- Project Name : ERP
 * -- Module Name :  Company Policy Category
 * -- Author : Fayas
 * -- Create date : 11 - May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CompanyPolicyCategory",
 *      required={""},
 *      @SWG\Property(
 *          property="companyPolicyCategoryID",
 *          description="companyPolicyCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyPolicyCategoryDescription",
 *          description="companyPolicyCategoryDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="applicableDocumentID",
 *          description="applicableDocumentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="impletemed",
 *          description="impletemed",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string"
 *      )
 * )
 */
class CompanyPolicyCategory extends Model
{

    public $table = 'erp_companypolicycategory';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'companyPolicyCategoryID';



    public $fillable = [
        'companyPolicyCategoryDescription',
        'policyCategoryComment',
        'applicableDocumentID',
        'documentID',
        'impletemed',
        'isActive',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyPolicyCategoryID' => 'integer',
        'companyPolicyCategoryDescription' => 'string',
        'policyCategoryComment' => 'string',
        'applicableDocumentID' => 'string',
        'documentID' => 'string',
        'impletemed' => 'string',
        'isActive' => 'integer',
        'timestamp' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
