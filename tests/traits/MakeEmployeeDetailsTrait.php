<?php

use Faker\Factory as Faker;
use App\Models\EmployeeDetails;
use App\Repositories\EmployeeDetailsRepository;

trait MakeEmployeeDetailsTrait
{
    /**
     * Create fake instance of EmployeeDetails and save it in database
     *
     * @param array $employeeDetailsFields
     * @return EmployeeDetails
     */
    public function makeEmployeeDetails($employeeDetailsFields = [])
    {
        /** @var EmployeeDetailsRepository $employeeDetailsRepo */
        $employeeDetailsRepo = App::make(EmployeeDetailsRepository::class);
        $theme = $this->fakeEmployeeDetailsData($employeeDetailsFields);
        return $employeeDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of EmployeeDetails
     *
     * @param array $employeeDetailsFields
     * @return EmployeeDetails
     */
    public function fakeEmployeeDetails($employeeDetailsFields = [])
    {
        return new EmployeeDetails($this->fakeEmployeeDetailsData($employeeDetailsFields));
    }

    /**
     * Get fake data of EmployeeDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEmployeeDetailsData($employeeDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'employeeSystemID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'empID' => $fake->word,
            'employeestatus' => $fake->randomDigitNotNull,
            'empimage' => $fake->word,
            'countryCode' => $fake->word,
            'expatOrLocal' => $fake->randomDigitNotNull,
            'SecondaryNationality' => $fake->word,
            'dateAssumed' => $fake->date('Y-m-d H:i:s'),
            'dateAssumed_O' => $fake->date('Y-m-d H:i:s'),
            'DOB' => $fake->date('Y-m-d H:i:s'),
            'DOB_O' => $fake->date('Y-m-d H:i:s'),
            'placeofBirth' => $fake->word,
            'placeofBirth_O' => $fake->word,
            'contactaddress1' => $fake->word,
            'contactaddress1_O' => $fake->word,
            'contactaddresscity' => $fake->word,
            'contactaddresscity_O' => $fake->word,
            'contactaddresscountry' => $fake->word,
            'contactaddresscountry_O' => $fake->word,
            'permenantaddress1' => $fake->word,
            'permenantaddress1_O' => $fake->word,
            'permenantaddresscity' => $fake->word,
            'permenantaddresscity_O' => $fake->word,
            'permenantaddresscountry' => $fake->word,
            'permenantaddresscountry_O' => $fake->word,
            'empLocation' => $fake->randomDigitNotNull,
            'locationTypeID' => $fake->randomDigitNotNull,
            'pasi_employercont' => $fake->randomDigitNotNull,
            'gender' => $fake->randomDigitNotNull,
            'pasiregno' => $fake->word,
            'pasi_employeecont' => $fake->randomDigitNotNull,
            'endOfContract' => $fake->date('Y-m-d H:i:s'),
            'endOfContract_O' => $fake->date('Y-m-d H:i:s'),
            'manpower_no' => $fake->word,
            'groupingID' => $fake->randomDigitNotNull,
            'holdSalary' => $fake->randomDigitNotNull,
            'categoryID' => $fake->randomDigitNotNull,
            'gradeID' => $fake->randomDigitNotNull,
            'schedulemasterID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->randomDigitNotNull,
            'functionalDepartmentID' => $fake->word,
            'employeesgradingmasterID' => $fake->randomDigitNotNull,
            'designationID' => $fake->randomDigitNotNull,
            'maritialStatus' => $fake->randomDigitNotNull,
            'maritalStatusDate' => $fake->date('Y-m-d H:i:s'),
            'noOfKids' => $fake->randomDigitNotNull,
            'SLBSeniority' => $fake->date('Y-m-d H:i:s'),
            'SLBSeniority_O' => $fake->date('Y-m-d H:i:s'),
            'WSISeniority' => $fake->date('Y-m-d H:i:s'),
            'WSISeniority_O' => $fake->date('Y-m-d H:i:s'),
            'salaryPayCurrency' => $fake->randomDigitNotNull,
            'isContract' => $fake->randomDigitNotNull,
            'isSSO' => $fake->randomDigitNotNull,
            'empTax' => $fake->randomDigitNotNull,
            'gratuityID' => $fake->randomDigitNotNull,
            'isPermenant' => $fake->randomDigitNotNull,
            'isRA' => $fake->randomDigitNotNull,
            'taxid' => $fake->randomDigitNotNull,
            'familyStatus' => $fake->randomDigitNotNull,
            'groupRAID' => $fake->randomDigitNotNull,
            'contractID' => $fake->randomDigitNotNull,
            'otcalculationHour' => $fake->randomDigitNotNull,
            'travelclaimcategoryID' => $fake->randomDigitNotNull,
            'newDepartmentID' => $fake->randomDigitNotNull,
            'medicalExaminationDate' => $fake->date('Y-m-d H:i:s'),
            'medicalExamiiationExpirydate' => $fake->date('Y-m-d H:i:s'),
            'isGeneral' => $fake->randomDigitNotNull,
            'rigAssigned' => $fake->randomDigitNotNull,
            'employeeCategoriesID' => $fake->randomDigitNotNull,
            'insuranceCode' => $fake->word,
            'insuranceTypeID' => $fake->randomDigitNotNull,
            'militaryServices' => $fake->randomDigitNotNull,
            'physicalStatus' => $fake->randomDigitNotNull,
            'isRehire' => $fake->randomDigitNotNull,
            'bloodTypeID' => $fake->randomDigitNotNull,
            'retireDate' => $fake->date('Y-m-d H:i:s'),
            'workHour' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $employeeDetailsFields);
    }
}
