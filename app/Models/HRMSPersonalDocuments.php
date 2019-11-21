<?php
/**
 * =============================================
 * -- File Name : HRMSPersonalDocuments.php
 * -- Project Name : ERP
 * -- Module Name : Employee
 * -- Author : Mohamed Rilwan
 * -- Create date : 20- November 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="HRMSPersonalDocuments",
 *      required={""},
 *      @SWG\Property(
 *          property="personaldocumentID",
 *          description="personaldocumentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentNo",
 *          description="documentNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="docIssuedby",
 *          description="docIssuedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="issueDate",
 *          description="issueDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="expireDate",
 *          description="expireDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="expireDate_O",
 *          description="expireDate_O",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="categoryID",
 *          description="categoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="attachmentFileName",
 *          description="attachmentFileName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdpc",
 *          description="createdpc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifieduser",
 *          description="modifieduser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedpc",
 *          description="modifiedpc",
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
class HRMSPersonalDocuments extends Model
{

    public $table = 'hrms_personaldocuments';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    public $fillable = [
        'documentType',
        'empID',
        'employeeSystemID',
        'documentNo',
        'docIssuedby',
        'issueDate',
        'expireDate',
        'expireDate_O',
        'categoryID',
        'attachmentFileName',
        'isActive',
        'createdUserGroup',
        'createdpc',
        'modifieduser',
        'modifiedpc',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'personaldocumentID' => 'integer',
        'documentType' => 'integer',
        'empID' => 'string',
        'employeeSystemID' => 'integer',
        'documentNo' => 'string',
        'docIssuedby' => 'string',
        'issueDate' => 'datetime',
        'expireDate' => 'datetime',
        'expireDate_O' => 'datetime',
        'categoryID' => 'integer',
        'attachmentFileName' => 'string',
        'isActive' => 'integer',
        'createdUserGroup' => 'string',
        'createdpc' => 'string',
        'modifieduser' => 'string',
        'modifiedpc' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'personaldocumentID' => 'required'
    ];

    
}
