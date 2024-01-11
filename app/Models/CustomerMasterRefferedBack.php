<?php
/**
 * =============================================
 * -- File Name : CustomerMasterRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name : Customer Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 18- December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerMasterRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="customerCodeSystemRefferedBack",
 *          description="customerCodeSystemRefferedBack",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCodeSystem",
 *          description="customerCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanySystemID",
 *          description="primaryCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanyID",
 *          description="primaryCompanyID",
 *          type="string"
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
 *          property="lastSerialOrder",
 *          description="lastSerialOrder",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="CutomerCode",
 *          description="CutomerCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerShortCode",
 *          description="customerShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="custGLAccountSystemID",
 *          description="custGLAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custGLaccount",
 *          description="custGLaccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CustomerName",
 *          description="CustomerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ReportTitle",
 *          description="ReportTitle",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerAddress1",
 *          description="customerAddress1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerAddress2",
 *          description="customerAddress2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCity",
 *          description="customerCity",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCountry",
 *          description="customerCountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="CustWebsite",
 *          description="CustWebsite",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="creditLimit",
 *          description="creditLimit",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditDays",
 *          description="creditDays",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerLogo",
 *          description="customerLogo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLinkedToSystemID",
 *          description="companyLinkedToSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLinkedTo",
 *          description="companyLinkedTo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isCustomerActive",
 *          description="isCustomerActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllowedQHSE",
 *          description="isAllowedQHSE",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatEligible",
 *          description="vatEligible",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatNumber",
 *          description="vatNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vatPercentage",
 *          description="vatPercentage",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSupplierForiegn",
 *          description="isSupplierForiegn",
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
 *          property="approvedEmpSystemID",
 *          description="approvedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedEmpID",
 *          description="approvedEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedComment",
 *          description="approvedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpSystemID",
 *          description="confirmedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpID",
 *          description="confirmedEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedEmpName",
 *          description="confirmedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
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
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      )
 * )
 */
class CustomerMasterRefferedBack extends Model
{

    public $table = 'customermaster_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'customerCodeSystemRefferedBack';



    public $fillable = [
        'customerCodeSystem',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
        'lastSerialOrder',
        'CutomerCode',
        'customerShortCode',
        'custGLAccountSystemID',
        'custGLaccount',
        'custUnbilledAccountSystemID',
        'custUnbilledAccount',
        'CustomerName',
        'ReportTitle',
        'customerAddress1',
        'customerAddress2',
        'customerCity',
        'customerCountry',
        'CustWebsite',
        'creditLimit',
        'creditDays',
        'customerLogo',
        'companyLinkedToSystemID',
        'companyLinkedTo',
        'isCustomerActive',
        'isAllowedQHSE',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'isSupplierForiegn',
        'approvedYN',
        'approvedEmpSystemID',
        'approvedEmpID',
        'approvedDate',
        'approvedComment',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedDate',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'createdPcID',
        'modifiedPc',
        'modifiedUser',
        'customer_registration_no',
        'customer_registration_expiry_date',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerCodeSystemRefferedBack' => 'integer',
        'customerCodeSystem' => 'integer',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'lastSerialOrder' => 'integer',
        'CutomerCode' => 'string',
        'customerShortCode' => 'string',
        'custGLAccountSystemID' => 'integer',
        'custGLaccount' => 'string',
        'custUnbilledAccountSystemID' => 'integer',
        'custUnbilledAccount' => 'string',
        'CustomerName' => 'string',
        'ReportTitle' => 'string',
        'customerAddress1' => 'string',
        'customerAddress2' => 'string',
        'customerCity' => 'string',
        'customerCountry' => 'string',
        'CustWebsite' => 'string',
        'creditLimit' => 'float',
        'creditDays' => 'integer',
        'customerLogo' => 'string',
        'companyLinkedToSystemID' => 'integer',
        'companyLinkedTo' => 'string',
        'isCustomerActive' => 'integer',
        'isAllowedQHSE' => 'integer',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vatPercentage' => 'integer',
        'isSupplierForiegn' => 'integer',
        'approvedYN' => 'integer',
        'approvedEmpSystemID' => 'integer',
        'approvedEmpID' => 'string',
        'approvedComment' => 'string',
        'confirmedYN' => 'integer',
        'confirmedEmpSystemID' => 'integer',
        'confirmedEmpID' => 'string',
        'confirmedEmpName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedPc' => 'string',
        'customer_registration_no' => 'string',
        'customer_registration_expiry_date' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function country(){
        return $this->belongsTo('App\Models\CountryMaster','customerCountry','countryID');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','approvedEmpSystemID','employeeSystemID');
    }
}
