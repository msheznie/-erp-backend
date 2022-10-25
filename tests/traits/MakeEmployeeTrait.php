<?php

use Faker\Factory as Faker;
use App\Models\Employee;
use App\Repositories\EmployeeRepository;

trait MakeEmployeeTrait
{
    /**
     * Create fake instance of Employee and save it in database
     *
     * @param array $employeeFields
     * @return Employee
     */
    public function makeEmployee($employeeFields = [])
    {
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = App::make(EmployeeRepository::class);
        $theme = $this->fakeEmployeeData($employeeFields);
        return $employeeRepo->create($theme);
    }

    /**
     * Get fake instance of Employee
     *
     * @param array $employeeFields
     * @return Employee
     */
    public function fakeEmployee($employeeFields = [])
    {
        return new Employee($this->fakeEmployeeData($employeeFields));
    }

    /**
     * Get fake data of Employee
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEmployeeData($employeeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'empID' => $fake->word,
            'serial' => $fake->randomDigitNotNull,
            'empLeadingText' => $fake->word,
            'empUserName' => $fake->word,
            'empTitle' => $fake->word,
            'empInitial' => $fake->word,
            'empName' => $fake->word,
            'empName_O' => $fake->word,
            'empFullName' => $fake->word,
            'empSurname' => $fake->word,
            'empSurname_O' => $fake->word,
            'empFirstName' => $fake->word,
            'empFirstName_O' => $fake->word,
            'empFamilyName' => $fake->word,
            'empFamilyName_O' => $fake->word,
            'empFatherName' => $fake->word,
            'empFatherName_O' => $fake->word,
            'empManagerAttached' => $fake->word,
            'empDateRegistered' => $fake->word,
            'empTelOffice' => $fake->word,
            'empTelMobile' => $fake->word,
            'empLandLineNo' => $fake->word,
            'extNo' => $fake->randomDigitNotNull,
            'empFax' => $fake->word,
            'empEmail' => $fake->word,
            'empLocation' => $fake->randomDigitNotNull,
            'empDateTerminated' => $fake->date('Y-m-d H:i:s'),
            'empLoginActive' => $fake->randomDigitNotNull,
            'empActive' => $fake->randomDigitNotNull,
            'userGroupID' => $fake->randomDigitNotNull,
            'empCompanyID' => $fake->word,
            'religion' => $fake->randomDigitNotNull,
            'isLoggedIn' => $fake->randomDigitNotNull,
            'isLoggedOutFailYN' => $fake->randomDigitNotNull,
            'logingFlag' => $fake->randomDigitNotNull,
            'isSuperAdmin' => $fake->randomDigitNotNull,
            'discharegedYN' => $fake->randomDigitNotNull,
            'hrusergroupID' => $fake->word,
            'isConsultant' => $fake->randomDigitNotNull,
            'isTrainee' => $fake->randomDigitNotNull,
            'is3rdParty' => $fake->randomDigitNotNull,
            '3rdPartyCompanyName' => $fake->word,
            'gender' => $fake->randomDigitNotNull,
            'designation' => $fake->randomDigitNotNull,
            'nationality' => $fake->word,
            'isManager' => $fake->randomDigitNotNull,
            'isApproval' => $fake->randomDigitNotNull,
            'isDashBoard' => $fake->randomDigitNotNull,
            'isAdmin' => $fake->randomDigitNotNull,
            'isBasicUser' => $fake->randomDigitNotNull,
            'ActivationCode' => $fake->word,
            'ActivationFlag' => $fake->randomDigitNotNull,
            'isHR_admin' => $fake->randomDigitNotNull,
            'isLock' => $fake->randomDigitNotNull,
            'opRptManagerAccess' => $fake->randomDigitNotNull,
            'isSupportAdmin' => $fake->randomDigitNotNull,
            'isHSEadmin' => $fake->randomDigitNotNull,
            'excludeObjectivesYN' => $fake->randomDigitNotNull,
            'machineID' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $employeeFields);
    }
}
