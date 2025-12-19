<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRDocumentDescriptionForms",
 *      required={""},
 *      @SWG\Property(
 *          property="DocDesFormID",
 *          description="DocDesFormID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DocDesSetID",
 *          description="DocDesSetID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DocDesID",
 *          description="DocDesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="subDocumentType",
 *          description="subDocumentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FormType",
 *          description="FormType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PersonType",
 *          description="PersonType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PersonID",
 *          description="PersonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FileName",
 *          description="FileName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UploadedDate",
 *          description="UploadedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="issueDate",
 *          description="issueDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="expireDate",
 *          description="expireDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="issuedBy",
 *          description="if value is -1 than get the value from issuedByText column will",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="issuedByText",
 *          description="issuedByText",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentNo",
 *          description="documentNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDeleted",
 *          description="isDeleted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isExpiryMailSend",
 *          description="isExpiryMailSend",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SchMasterID",
 *          description="SchMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BranchID",
 *          description="BranchID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Erp_companyID",
 *          description="Erp_companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="AcademicYearID",
 *          description="AcademicYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSubmitted",
 *          description="isSubmitted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CreatedUserID",
 *          description="CreatedUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CreatedUserName",
 *          description="CreatedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CreatedDate",
 *          description="CreatedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="CreatedPC",
 *          description="CreatedPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedUserID",
 *          description="ModifiedUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedUserName",
 *          description="ModifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedDateTime",
 *          description="ModifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedPC",
 *          description="ModifiedPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Timestamp",
 *          description="Timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class HRDocumentDescriptionForms extends Model
{

    public $table = 'srp_documentdescriptionforms';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'DocDesSetID',
        'DocDesID',
        'subDocumentType',
        'FormType',
        'PersonType',
        'PersonID',
        'FileName',
        'UploadedDate',
        'issueDate',
        'expireDate',
        'issuedBy',
        'issuedByText',
        'documentNo',
        'isActive',
        'isDeleted',
        'isExpiryMailSend',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'AcademicYearID',
        'isSubmitted',
        'CreatedUserID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserID',
        'ModifiedUserName',
        'ModifiedDateTime',
        'ModifiedPC',
        'Timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'DocDesFormID' => 'integer',
        'DocDesSetID' => 'integer',
        'DocDesID' => 'integer',
        'subDocumentType' => 'integer',
        'FormType' => 'string',
        'PersonType' => 'string',
        'PersonID' => 'integer',
        'FileName' => 'string',
        'UploadedDate' => 'datetime',
        'issueDate' => 'date',
        'expireDate' => 'date',
        'issuedBy' => 'integer',
        'issuedByText' => 'string',
        'documentNo' => 'string',
        'isActive' => 'integer',
        'isDeleted' => 'integer',
        'isExpiryMailSend' => 'integer',
        'SchMasterID' => 'integer',
        'BranchID' => 'integer',
        'Erp_companyID' => 'integer',
        'AcademicYearID' => 'integer',
        'isSubmitted' => 'integer',
        'CreatedUserID' => 'integer',
        'CreatedUserName' => 'string',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'ModifiedUserID' => 'integer',
        'ModifiedUserName' => 'string',
        'ModifiedDateTime' => 'datetime',
        'ModifiedPC' => 'string',
        'Timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    function master(){
        return $this->belongsTo(HRDocumentDescriptionMaster::class, 'DocDesID', 'DocDesID');
    }

    function employee(){
        return $this->belongsTo(SrpEmployeeDetails::class, 'PersonID', 'EIdNo');
    }
    
}
