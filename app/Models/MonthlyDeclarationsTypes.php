<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MonthlyDeclarationsTypes",
 *      required={""},
 *      @SWG\Property(
 *          property="monthlyDeclarationID",
 *          description="monthlyDeclarationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthlyDeclaration",
 *          description="monthlyDeclaration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="monthlyDeclarationType",
 *          description="monthlyDeclarationType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="salaryCategoryID",
 *          description="salaryCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLCode",
 *          description="expenseGLCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPayrollCategory",
 *          description="1 => Payroll Cateogery | 2=> Non Payroll Category",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class MonthlyDeclarationsTypes extends Model
{

    public $table = 'srp_erp_pay_monthlydeclarationstypes';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'monthlyDeclaration',
        'monthlyDeclarationType',
        'salaryCategoryID',
        'expenseGLCode',
        'isPayrollCategory',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'monthlyDeclarationID' => 'integer',
        'monthlyDeclaration' => 'string',
        'monthlyDeclarationType' => 'string',
        'salaryCategoryID' => 'integer',
        'expenseGLCode' => 'integer',
        'isPayrollCategory' => 'boolean',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'isPayrollCategory' => 'required'
    ];

    
}
