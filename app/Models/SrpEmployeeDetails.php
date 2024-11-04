<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SrpEmployeeDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="EIdNo",
 *          description="EIdNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="ECode",
 *          description="ECode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EmpSecondaryCode",
 *          description="EmpSecondaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EmpTitleId",
 *          description="EmpTitleId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="manPowerNo",
 *          description="manPowerNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ssoNo",
 *          description="ssoNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EmpDesignationId",
 *          description="EmpDesignationId",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename1",
 *          description="Ename1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename2",
 *          description="Ename2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="AirportDestinationID",
 *          description="AirportDestinationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Ename3",
 *          description="Ename3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Ename4",
 *          description="Ename4",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empSecondName",
 *          description="empSecondName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EFamilyName",
 *          description="EFamilyName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="initial",
 *          description="initial",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EmpShortCode",
 *          description="EmpShortCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Enameother1",
 *          description="Enameother1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Enameother2",
 *          description="Enameother2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Enameother3",
 *          description="Enameother3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Enameother4",
 *          description="Enameother4",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empSecondNameOther",
 *          description="empSecondNameOther",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EFamilyNameOther",
 *          description="EFamilyNameOther",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empSignature",
 *          description="empSignature",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EmpImage",
 *          description="EmpImage",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EthumbnailImage",
 *          description="EthumbnailImage",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Gender",
 *          description="Gender",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="payee_emp_type",
 *          description="payee_emp_type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="EpAddress1",
 *          description="EpAddress1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EpAddress2",
 *          description="EpAddress2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EpAddress3",
 *          description="EpAddress3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EpAddress4",
 *          description="EpAddress4",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ZipCode",
 *          description="ZipCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EpTelephone",
 *          description="EpTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EpFax",
 *          description="EpFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EpMobile",
 *          description="EpMobile",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcAddress1",
 *          description="EcAddress1",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcAddress2",
 *          description="EcAddress2",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcAddress3",
 *          description="EcAddress3",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcAddress4",
 *          description="EcAddress4",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcPOBox",
 *          description="EcPOBox",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcPC",
 *          description="EcPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcArea",
 *          description="EcArea",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcTel",
 *          description="EcTel",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcExtension",
 *          description="EcExtension",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcFax",
 *          description="EcFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EcMobile",
 *          description="EcMobile",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EEmail",
 *          description="EEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="personalEmail",
 *          description="personalEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EDOB",
 *          description="EDOB",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="EDOJ",
 *          description="EDOJ",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="NIC",
 *          description="National Idinticatd No",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="insuranceNo",
 *          description="insuranceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EPassportNO",
 *          description="EPassportNO",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="EPassportExpiryDate",
 *          description="EPassportExpiryDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="EVisaExpiryDate",
 *          description="EVisaExpiryDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="Nid",
 *          description="Nid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Rid",
 *          description="Rid",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="AirportDestination",
 *          description="AirportDestination",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="travelFrequencyID",
 *          description="FK - srp_erp_travelfrequency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="commissionSchemeID",
 *          description="fk => srp_erp_pay_commissionscheme.id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="medicalInfo",
 *          description="medicalInfo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="SchMasterId",
 *          description="SchMasterId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="branchID",
 *          description="branchID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="userType",
 *          description="0- Basic User  1 - module User",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSystemUserYN",
 *          description="Differentiate system user for TIE",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="UserName",
 *          description="UserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Password",
 *          description="Password",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDeleted",
 *          description="isDeleted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="HouseID",
 *          description="HouseID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="HouseCatID",
 *          description="HouseCatID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="HPID",
 *          description="HPID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPayrollEmployee",
 *          description="1 yes 0 no",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payCurrencyID",
 *          description="payCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payCurrency",
 *          description="payCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isLeft",
 *          description="isLeft",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DateLeft",
 *          description="DateLeft",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="LeftComment",
 *          description="LeftComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BloodGroup",
 *          description="BloodGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="DateAssumed",
 *          description="DateAssumed",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="probationPeriod",
 *          description="probationPeriod",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="isDischarged",
 *          description="IF yes -1 else 0",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dischargedByEmpID",
 *          description="dischargedByEmpID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="EmployeeConType",
 *          description="EmployeeConType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dischargedDate",
 *          description="dischargedDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="lastWorkingDate",
 *          description="lastWorkingDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="gratuityCalculationDate",
 *          description="gratuityCalculationDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="dischargeTypeID",
 *          description="dischargeTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dischargeReasonID",
 *          description="dischargeReasonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="dischargedComment",
 *          description="dischargedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="eligibleToRehire",
 *          description="eligibleToRehire",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="finalSettlementDoneYN",
 *          description="0- no 1- yes",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="MaritialStatus",
 *          description="MaritialStatus",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="Nationality",
 *          description="Nationality",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isLoginAttempt",
 *          description="isLoginAttempt",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isChangePassword",
 *          description="isChangePassword",
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
 *          property="ModifiedUserName",
 *          description="ModifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Timestamp",
 *          description="Timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="ModifiedPC",
 *          description="ModifiedPC",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="NoOfLoginAttempt",
 *          description="NoOfLoginAttempt",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="languageID",
 *          description="languageID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="locationID",
 *          description="locationID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sponsorID",
 *          description="sponsorID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mobileCreditLimit",
 *          description="mobileCreditLimit",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
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
 *          property="insurance_category",
 *          description="insurance_category",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="insurance_code",
 *          description="insurance_code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="cover_from",
 *          description="cover_from",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="cover_to",
 *          description="cover_to",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="floorID",
 *          description="floorID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deviceID",
 *          description="Attendance machine",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="empMachineID",
 *          description="Payroll Machine Employee Auto ID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="leaveGroupID",
 *          description="leaveGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMobileCheckIn",
 *          description="isMobileCheckIn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCheckin",
 *          description="isCheckin",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="token",
 *          description="token",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="overTimeGroup",
 *          description="overTimeGroup",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="familyStatusID",
 *          description="familyStatusID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="gratuityID",
 *          description="FK srp_erp_pay_gratuitymaster.gratuityID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSystemAdmin",
 *          description="isSystemAdmin",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isHRAdmin",
 *          description="isHRAdmin",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractStartDate",
 *          description="contractStartDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="contractEndDate",
 *          description="contractEndDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="contractRefNo",
 *          description="contractRefNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="empConfirmDate",
 *          description="empConfirmDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="empConfirmedYN",
 *          description="empConfirmedYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="rejoinDate",
 *          description="rejoinDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="previousEmpID",
 *          description="previousEmpID",
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
 *          property="pos_userGroupMasterID",
 *          description="pos_userGroupMasterID",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="pos_userGroupMasterID_gpos",
 *          description="pos_userGroupMasterID_gpos",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="pos_barCode",
 *          description="pos_barCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isLocalPosSyncEnable",
 *          description="isLocalPosSyncEnable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isLocalPosSalesRptEnable",
 *          description="isLocalPosSalesRptEnable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tibianType",
 *          description="Fk => srp_erp_tibian_employeetype.id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="LocalPOSUserType",
 *          description="LocalPOSUserType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="last_login",
 *          description="last_login",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="exclude_PASI",
 *          description="Exclude PASI computation for joined month",
 *          type="boolean"
 *      )
 * )
 */
class SrpEmployeeDetails extends Model
{

    public $table = 'srp_employeesdetails';

    protected $primaryKey = 'EIdNo';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['signatureURL'];


    public $fillable = [
        'serialNo',
        'ECode',
        'EmpSecondaryCode',
        'EmpTitleId',
        'manPowerNo',
        'ssoNo',
        'EmpDesignationId',
        'Ename1',
        'Ename2',
        'AirportDestinationID',
        'Ename3',
        'Ename4',
        'empSecondName',
        'EFamilyName',
        'initial',
        'EmpShortCode',
        'Enameother1',
        'Enameother2',
        'Enameother3',
        'Enameother4',
        'empSecondNameOther',
        'EFamilyNameOther',
        'empSignature',
        'EmpImage',
        'EthumbnailImage',
        'Gender',
        'payee_emp_type',
        'EpAddress1',
        'EpAddress2',
        'EpAddress3',
        'EpAddress4',
        'ZipCode',
        'EpTelephone',
        'EpFax',
        'EpMobile',
        'EcAddress1',
        'EcAddress2',
        'EcAddress3',
        'EcAddress4',
        'EcPOBox',
        'EcPC',
        'EcArea',
        'EcTel',
        'EcExtension',
        'EcFax',
        'EcMobile',
        'EEmail',
        'personalEmail',
        'EDOB',
        'EDOJ',
        'NIC',
        'insuranceNo',
        'EPassportNO',
        'EPassportExpiryDate',
        'EVisaExpiryDate',
        'Nid',
        'Rid',
        'AirportDestination',
        'travelFrequencyID',
        'commissionSchemeID',
        'medicalInfo',
        'SchMasterId',
        'branchID',
        'userType',
        'isSystemUserYN',
        'UserName',
        'Password',
        'isDeleted',
        'HouseID',
        'HouseCatID',
        'HPID',
        'isPayrollEmployee',
        'payCurrencyID',
        'payCurrency',
        'isLeft',
        'DateLeft',
        'LeftComment',
        'BloodGroup',
        'DateAssumed',
        'probationPeriod',
        'isDischarged',
        'dischargedByEmpID',
        'EmployeeConType',
        'dischargedDate',
        'lastWorkingDate',
        'gratuityCalculationDate',
        'dischargeTypeID',
        'dischargeReasonID',
        'dischargedComment',
        'eligibleToRehire',
        'finalSettlementDoneYN',
        'MaritialStatus',
        'Nationality',
        'isLoginAttempt',
        'isChangePassword',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC',
        'isActive',
        'NoOfLoginAttempt',
        'languageID',
        'locationID',
        'sponsorID',
        'mobileCreditLimit',
        'segmentID',
        'Erp_companyID',
        'insurance_category',
        'insurance_code',
        'cover_from',
        'cover_to',
        'floorID',
        'deviceID',
        'empMachineID',
        'leaveGroupID',
        'isMobileCheckIn',
        'isCheckin',
        'token',
        'overTimeGroup',
        'familyStatusID',
        'gratuityID',
        'isSystemAdmin',
        'isHRAdmin',
        'contractStartDate',
        'contractEndDate',
        'contractRefNo',
        'empConfirmDate',
        'empConfirmedYN',
        'rejoinDate',
        'previousEmpID',
        'gradeID',
        'pos_userGroupMasterID',
        'pos_userGroupMasterID_gpos',
        'pos_barCode',
        'isLocalPosSyncEnable',
        'isLocalPosSalesRptEnable',
        'tibianType',
        'LocalPOSUserType',
        'last_login',
        'exclude_PASI'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'EIdNo' => 'integer',
        'serialNo' => 'integer',
        'ECode' => 'string',
        'EmpSecondaryCode' => 'string',
        'EmpTitleId' => 'integer',
        'manPowerNo' => 'string',
        'ssoNo' => 'string',
        'EmpDesignationId' => 'string',
        'Ename1' => 'string',
        'Ename2' => 'string',
        'AirportDestinationID' => 'integer',
        'Ename3' => 'string',
        'Ename4' => 'string',
        'empSecondName' => 'string',
        'EFamilyName' => 'string',
        'initial' => 'string',
        'EmpShortCode' => 'string',
        'Enameother1' => 'string',
        'Enameother2' => 'string',
        'Enameother3' => 'string',
        'Enameother4' => 'string',
        'empSecondNameOther' => 'string',
        'EFamilyNameOther' => 'string',
        'empSignature' => 'string',
        'signatureURL' => 'string',
        'EmpImage' => 'string',
        'EthumbnailImage' => 'string',
        'Gender' => 'string',
        'payee_emp_type' => 'integer',
        'EpAddress1' => 'string',
        'EpAddress2' => 'string',
        'EpAddress3' => 'string',
        'EpAddress4' => 'string',
        'ZipCode' => 'string',
        'EpTelephone' => 'string',
        'EpFax' => 'string',
        'EpMobile' => 'string',
        'EcAddress1' => 'string',
        'EcAddress2' => 'string',
        'EcAddress3' => 'string',
        'EcAddress4' => 'string',
        'EcPOBox' => 'string',
        'EcPC' => 'string',
        'EcArea' => 'string',
        'EcTel' => 'string',
        'EcExtension' => 'string',
        'EcFax' => 'string',
        'EcMobile' => 'string',
        'EEmail' => 'string',
        'personalEmail' => 'string',
        'EDOB' => 'date',
        'EDOJ' => 'date',
        'NIC' => 'string',
        'insuranceNo' => 'string',
        'EPassportNO' => 'string',
        'EPassportExpiryDate' => 'date',
        'EVisaExpiryDate' => 'date',
        'Nid' => 'integer',
        'Rid' => 'integer',
        'AirportDestination' => 'string',
        'travelFrequencyID' => 'integer',
        'commissionSchemeID' => 'integer',
        'medicalInfo' => 'string',
        'SchMasterId' => 'integer',
        'branchID' => 'integer',
        'userType' => 'integer',
        'isSystemUserYN' => 'integer',
        'UserName' => 'string',
        'Password' => 'string',
        'isDeleted' => 'integer',
        'HouseID' => 'integer',
        'HouseCatID' => 'integer',
        'HPID' => 'integer',
        'isPayrollEmployee' => 'integer',
        'payCurrencyID' => 'integer',
        'payCurrency' => 'string',
        'isLeft' => 'integer',
        'DateLeft' => 'date',
        'LeftComment' => 'string',
        'BloodGroup' => 'integer',
        'DateAssumed' => 'date',
        'probationPeriod' => 'date',
        'isDischarged' => 'integer',
        'dischargedByEmpID' => 'integer',
        'EmployeeConType' => 'integer',
        'dischargedDate' => 'date',
        'lastWorkingDate' => 'date',
        'gratuityCalculationDate' => 'date',
        'dischargeTypeID' => 'integer',
        'dischargeReasonID' => 'integer',
        'dischargedComment' => 'string',
        'eligibleToRehire' => 'boolean',
        'finalSettlementDoneYN' => 'integer',
        'MaritialStatus' => 'integer',
        'Nationality' => 'integer',
        'isLoginAttempt' => 'integer',
        'isChangePassword' => 'integer',
        'CreatedUserName' => 'string',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'ModifiedUserName' => 'string',
        'Timestamp' => 'datetime',
        'ModifiedPC' => 'string',
        'isActive' => 'integer',
        'NoOfLoginAttempt' => 'integer',
        'languageID' => 'integer',
        'locationID' => 'integer',
        'sponsorID' => 'integer',
        'mobileCreditLimit' => 'float',
        'segmentID' => 'integer',
        'Erp_companyID' => 'integer',
        'insurance_category' => 'integer',
        'insurance_code' => 'string',
        'cover_from' => 'datetime',
        'cover_to' => 'datetime',
        'floorID' => 'integer',
        'deviceID' => 'integer',
        'empMachineID' => 'integer',
        'leaveGroupID' => 'integer',
        'isMobileCheckIn' => 'integer',
        'isCheckin' => 'integer',
        'token' => 'string',
        'overTimeGroup' => 'integer',
        'familyStatusID' => 'integer',
        'gratuityID' => 'integer',
        'isSystemAdmin' => 'integer',
        'isHRAdmin' => 'integer',
        'contractStartDate' => 'date',
        'contractEndDate' => 'date',
        'contractRefNo' => 'string',
        'empConfirmDate' => 'date',
        'empConfirmedYN' => 'boolean',
        'rejoinDate' => 'date',
        'previousEmpID' => 'integer',
        'gradeID' => 'integer',
        'pos_userGroupMasterID' => 'boolean',
        'pos_userGroupMasterID_gpos' => 'boolean',
        'pos_barCode' => 'string',
        'isLocalPosSyncEnable' => 'integer',
        'isLocalPosSalesRptEnable' => 'integer',
        'tibianType' => 'integer',
        'LocalPOSUserType' => 'string',
        'last_login' => 'datetime',
        'exclude_PASI' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function currency()
    {
        return $this->belongsTo(CurrencyMaster::class, 'payCurrencyID', 'currencyID');
    }

    public function designation()
    {
        return $this->belongsTo(HrmsDesignation::class, 'EmpDesignationId', 'DesignationID');
    }

    public function manager()
    {
        return $this->hasOne(HrmsEmployeeManager::class, 'empID', 'EIdNo');
    }

    public function getSignatureURLAttribute() {

        if ($this->empSignature != null) {
            return Helper::getFileUrlFromS3($this->empSignature);
        } else {
            return null;
        }
    }

}
