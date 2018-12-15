<?php
/**
 * =============================================
 * -- File Name : DocumentRestrictionAssign.php
 * -- Project Name : ERP
 * -- Module Name :  Document Restriction Assign
 * -- Author : Fayas
 * -- Create date : 14 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DocumentRestrictionAssign",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentRestrictionPolicyID",
 *          description="documentRestrictionPolicyID",
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
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="userGroupID",
 *          description="userGroupID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class DocumentRestrictionAssign extends Model
{

    public $table = 'documentrestrictionassign';

    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;




    public $fillable = [
        'documentRestrictionPolicyID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'companyID',
        'userGroupID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'documentRestrictionPolicyID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'userGroupID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    
}
