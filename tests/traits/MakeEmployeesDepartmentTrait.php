<?php

use Faker\Factory as Faker;
use App\Models\EmployeesDepartment;
use App\Repositories\EmployeesDepartmentRepository;

trait MakeEmployeesDepartmentTrait
{
    /**
     * Create fake instance of EmployeesDepartment and save it in database
     *
     * @param array $employeesDepartmentFields
     * @return EmployeesDepartment
     */
    public function makeEmployeesDepartment($employeesDepartmentFields = [])
    {
        /** @var EmployeesDepartmentRepository $employeesDepartmentRepo */
        $employeesDepartmentRepo = App::make(EmployeesDepartmentRepository::class);
        $theme = $this->fakeEmployeesDepartmentData($employeesDepartmentFields);
        return $employeesDepartmentRepo->create($theme);
    }

    /**
     * Get fake instance of EmployeesDepartment
     *
     * @param array $employeesDepartmentFields
     * @return EmployeesDepartment
     */
    public function fakeEmployeesDepartment($employeesDepartmentFields = [])
    {
        return new EmployeesDepartment($this->fakeEmployeesDepartmentData($employeesDepartmentFields));
    }

    /**
     * Get fake data of EmployeesDepartment
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEmployeesDepartmentData($employeesDepartmentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'employeeSystemID' => $fake->randomDigitNotNull,
            'employeeID' => $fake->word,
            'employeeGroupID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyId' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'departmentID' => $fake->word,
            'ServiceLineSystemID' => $fake->randomDigitNotNull,
            'ServiceLineID' => $fake->word,
            'warehouseSystemCode' => $fake->randomDigitNotNull,
            'reportingManagerID' => $fake->word,
            'isDefault' => $fake->randomDigitNotNull,
            'dischargedYN' => $fake->randomDigitNotNull,
            'approvalDeligated' => $fake->randomDigitNotNull,
            'approvalDeligatedFromEmpID' => $fake->word,
            'approvalDeligatedFrom' => $fake->word,
            'approvalDeligatedTo' => $fake->word,
            'dmsIsUploadEnable' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $employeesDepartmentFields);
    }
}
