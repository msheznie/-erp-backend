<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HrMonthlyDeductionMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="monthlyDeductionMasterID",
 *          description="monthlyDeductionMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="monthlyDeductionCode",
 *          description="monthlyDeductionCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="payrollGroup",
 *          description="srp_erp_hrperiodgroup.hrGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currencyID",
 *          description="currencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="dateMD",
 *          description="dateMD",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="isNonPayroll",
 *          description="isNonPayroll",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isProcessed",
 *          description="isProcessed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="currentApprovalLevel",
 *          description="currentApprovalLevel",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="currentLevelNo",
 *          description="currentLevelNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedbyEmpID",
 *          description="approvedbyEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedbyEmpName",
 *          description="approvedbyEmpName",
 *          type="string"
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
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentCode",
 *          description="segmentCode",
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
class HrMonthlyDeductionMaster extends Model
{

    public $table = 'srp_erp_pay_monthlydeductionmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
        'monthlyDeductionCode',
        'serialNo',
        'documentID',
        'payrollGroup',
        'description',
        'currencyID',
        'currency',
        'dateMD',
        'isNonPayroll',
        'pv_id',
        'isProcessed',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'currentApprovalLevel',
        'approvedYN',
        'supplierInvoiceID',
        'approvedDate',
        'currentLevelNo',
        'approvedbyEmpID',
        'approvedbyEmpName',
        'companyID',
        'companyCode',
        'segmentID',
        'segmentCode',
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
        'monthlyDeductionMasterID' => 'integer',
        'monthlyDeductionCode' => 'string',
        'serialNo' => 'integer',
        'documentID' => 'string',
        'payrollGroup' => 'integer',
        'description' => 'string',
        'currencyID' => 'integer',
        'currency' => 'string',
        'dateMD' => 'date',
        'isNonPayroll' => 'string',
        'pv_id' => 'integer',
        'isProcessed' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'confirmedDate' => 'date',
        'currentApprovalLevel' => 'integer',
        'supplierInvoiceID' => 'integer',
        'approvedYN' => 'integer',
        'approvedDate' => 'datetime',
        'currentLevelNo' => 'integer',
        'approvedbyEmpID' => 'string',
        'approvedbyEmpName' => 'string',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
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
        'currencyID' => 'required',
        'timestamp' => 'required'
    ];

    
}
