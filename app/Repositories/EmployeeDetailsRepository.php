<?php

namespace App\Repositories;

use App\Models\EmployeeDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EmployeeDetailsRepository
 * @package App\Repositories
 * @version July 26, 2018, 8:37 am UTC
 *
 * @method EmployeeDetails findWithoutFail($id, $columns = ['*'])
 * @method EmployeeDetails find($id, $columns = ['*'])
 * @method EmployeeDetails first($columns = ['*'])
*/
class EmployeeDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
     * Configure the Model
     **/
    public function model()
    {
        return EmployeeDetails::class;
    }
}
