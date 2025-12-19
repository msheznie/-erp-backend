<?php
/**
 * =============================================
 * -- File Name : EmployeeDetails.php
 * -- Project Name : ERP
 * -- Module Name : Employee Details
 * -- Author : Mohamed Fayas
 * -- Create date : 26- July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EmployeeDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="employeedetailsID",
 *          description="employeedetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeSystemID",
 *          description="employeeSystemID",
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
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeestatus",
 *          description="employeestatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empimage",
 *          description="empimage",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryCode",
 *          description="countryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expatOrLocal",
 *          description="expatOrLocal",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="SecondaryNationality",
 *          description="SecondaryNationality",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="placeofBirth",
 *          description="placeofBirth",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="placeofBirth_O",
 *          description="placeofBirth_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactaddress1",
 *          description="contactaddress1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactaddress1_O",
 *          description="contactaddress1_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactaddresscity",
 *          description="contactaddresscity",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactaddresscity_O",
 *          description="contactaddresscity_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactaddresscountry",
 *          description="contactaddresscountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactaddresscountry_O",
 *          description="contactaddresscountry_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="permenantaddress1",
 *          description="permenantaddress1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="permenantaddress1_O",
 *          description="permenantaddress1_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="permenantaddresscity",
 *          description="permenantaddresscity",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="permenantaddresscity_O",
 *          description="permenantaddresscity_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="permenantaddresscountry",
 *          description="permenantaddresscountry",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="permenantaddresscountry_O",
 *          description="permenantaddresscountry_O",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empLocation",
 *          description="empLocation",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="locationTypeID",
 *          description="locationTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pasi_employercont",
 *          description="pasi_employercont",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="gender",
 *          description="gender",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pasiregno",
 *          description="pasiregno",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="pasi_employeecont",
 *          description="pasi_employeecont",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="manpower_no",
 *          description="manpower_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="groupingID",
 *          description="groupingID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="holdSalary",
 *          description="holdSalary",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="categoryID",
 *          description="categoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="gradeID",
 *          description="gradeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="schedulemasterID",
 *          description="schedulemasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="functionalDepartmentID",
 *          description="functionalDepartmentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeesgradingmasterID",
 *          description="employeesgradingmasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="designationID",
 *          description="designationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="maritialStatus",
 *          description="maritialStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="noOfKids",
 *          description="noOfKids",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salaryPayCurrency",
 *          description="salaryPayCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isContract",
 *          description="isContract",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSSO",
 *          description="isSSO",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empTax",
 *          description="empTax",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="gratuityID",
 *          description="gratuityID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPermenant",
 *          description="isPermenant",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRA",
 *          description="isRA",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxid",
 *          description="taxid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="familyStatus",
 *          description="familyStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="groupRAID",
 *          description="groupRAID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="otcalculationHour",
 *          description="otcalculationHour",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="travelclaimcategoryID",
 *          description="travelclaimcategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="newDepartmentID",
 *          description="newDepartmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isGeneral",
 *          description="isGeneral",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rigAssigned",
 *          description="rigAssigned",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="employeeCategoriesID",
 *          description="employeeCategoriesID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="insuranceCode",
 *          description="insuranceCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="insuranceTypeID",
 *          description="insuranceTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="militaryServices",
 *          description="militaryServices",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="physicalStatus",
 *          description="physicalStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRehire",
 *          description="isRehire",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bloodTypeID",
 *          description="bloodTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="workHour",
 *          description="workHour",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
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
class EmployeeDetails extends Model
{

    public $table = 'hrms_employeedetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'employeedetailsID';


    public $fillable = [
        'employeeSystemID',
        'companySystemID',
        'companyID',
        'empID',
        'employeestatus',
        'empimage',
        'countryCode',
        'expatOrLocal',
        'SecondaryNationality',
        'dateAssumed',
        'dateAssumed_O',
        'DOB',
        'DOB_O',
        'placeofBirth',
        'placeofBirth_O',
        'contactaddress1',
        'contactaddress1_O',
        'contactaddresscity',
        'contactaddresscity_O',
        'contactaddresscountry',
        'contactaddresscountry_O',
        'permenantaddress1',
        'permenantaddress1_O',
        'permenantaddresscity',
        'permenantaddresscity_O',
        'permenantaddresscountry',
        'permenantaddresscountry_O',
        'empLocation',
        'locationTypeID',
        'pasi_employercont',
        'gender',
        'pasiregno',
        'pasi_employeecont',
        'endOfContract',
        'endOfContract_O',
        'manpower_no',
        'groupingID',
        'holdSalary',
        'categoryID',
        'gradeID',
        'schedulemasterID',
        'departmentID',
        'functionalDepartmentID',
        'employeesgradingmasterID',
        'designationID',
        'maritialStatus',
        'maritalStatusDate',
        'noOfKids',
        'SLBSeniority',
        'SLBSeniority_O',
        'WSISeniority',
        'WSISeniority_O',
        'salaryPayCurrency',
        'isContract',
        'isSSO',
        'empTax',
        'gratuityID',
        'isPermenant',
        'isRA',
        'taxid',
        'familyStatus',
        'groupRAID',
        'contractID',
        'otcalculationHour',
        'travelclaimcategoryID',
        'newDepartmentID',
        'medicalExaminationDate',
        'medicalExamiiationExpirydate',
        'isGeneral',
        'rigAssigned',
        'employeeCategoriesID',
        'insuranceCode',
        'insuranceTypeID',
        'militaryServices',
        'physicalStatus',
        'isRehire',
        'bloodTypeID',
        'retireDate',
        'workHour',
        'createdUserGroup',
        'createdPCid',
        'createdUserID',
        'modifiedUser',
        'modifiedPc',
        'createdDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeedetailsID' => 'integer',
        'employeeSystemID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'empID' => 'string',
        'employeestatus' => 'integer',
        'empimage' => 'string',
        'countryCode' => 'string',
        'expatOrLocal' => 'integer',
        'SecondaryNationality' => 'string',
        'placeofBirth' => 'string',
        'placeofBirth_O' => 'string',
        'contactaddress1' => 'string',
        'contactaddress1_O' => 'string',
        'contactaddresscity' => 'string',
        'contactaddresscity_O' => 'string',
        'contactaddresscountry' => 'string',
        'contactaddresscountry_O' => 'string',
        'permenantaddress1' => 'string',
        'permenantaddress1_O' => 'string',
        'permenantaddresscity' => 'string',
        'permenantaddresscity_O' => 'string',
        'permenantaddresscountry' => 'string',
        'permenantaddresscountry_O' => 'string',
        'empLocation' => 'integer',
        'locationTypeID' => 'integer',
        'pasi_employercont' => 'float',
        'gender' => 'integer',
        'pasiregno' => 'string',
        'pasi_employeecont' => 'float',
        'manpower_no' => 'string',
        'groupingID' => 'integer',
        'holdSalary' => 'integer',
        'categoryID' => 'integer',
        'gradeID' => 'integer',
        'schedulemasterID' => 'integer',
        'departmentID' => 'integer',
        'functionalDepartmentID' => 'string',
        'employeesgradingmasterID' => 'integer',
        'designationID' => 'integer',
        'maritialStatus' => 'integer',
        'noOfKids' => 'integer',
        'salaryPayCurrency' => 'integer',
        'isContract' => 'integer',
        'isSSO' => 'integer',
        'empTax' => 'integer',
        'gratuityID' => 'integer',
        'isPermenant' => 'integer',
        'isRA' => 'integer',
        'taxid' => 'integer',
        'familyStatus' => 'integer',
        'groupRAID' => 'integer',
        'contractID' => 'integer',
        'otcalculationHour' => 'integer',
        'travelclaimcategoryID' => 'integer',
        'newDepartmentID' => 'integer',
        'isGeneral' => 'integer',
        'rigAssigned' => 'integer',
        'employeeCategoriesID' => 'integer',
        'insuranceCode' => 'string',
        'insuranceTypeID' => 'integer',
        'militaryServices' => 'integer',
        'physicalStatus' => 'integer',
        'isRehire' => 'integer',
        'bloodTypeID' => 'integer',
        'workHour' => 'integer',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'createdUserID' => 'string',
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

    public function designation(){
        return $this->hasOne('App\Models\Designation', 'designationID','designationID');
    }

    public function maritial_status(){
        return $this->hasOne('App\Models\MaritialStatus','maritialstatusID','maritialStatus');
    }

    public function country(){
        return $this->hasOne('App\Models\CountryMaster','countryCode','countryCode');
    }

    public function schedule(){
        return $this->hasOne('App\Models\ScheduleMaster','schedulemasterID','schedulemasterID');
    }

    public function departmentMaster(){
        return $this->belongsTo('App\Models\DepartmentMaster','functionalDepartmentID','DepartmentID');
    }

    public function hrmsDepartmentMaster(){
        return $this->belongsTo('App\Models\HrmsDepartmentMaster','departmentID','DepartmentID');
    }

    public function employeeMaster(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }
}
