<?php
/**
 * =============================================
 * -- File Name : ReportTemplateCashBank.php
 * -- Project Name : ERP
 * -- Module Name : Configuration
 * -- Author : Mohamed Mubashir
 * -- Create date : 18- January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ReportTemplateCashBank",
 *      required={""},
 *      @SWG\Property(
 *          property="cashBankID",
 *          description="cashBankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeDescription",
 *          description="glCodeDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
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
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class ReportTemplateCashBank extends Model
{

    public $table = 'erp_companyreporttemplatecashbank';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'cashBankID';

    public $fillable = [
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDescription',
        'isActive',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'cashBankID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glCodeDescription' => 'string',
        'isActive' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
