<?php

use Faker\Factory as Faker;
use App\Models\EmployeeNavigation;
use App\Repositories\EmployeeNavigationRepository;

trait MakeEmployeeNavigationTrait
{
    /**
     * Create fake instance of EmployeeNavigation and save it in database
     *
     * @param array $employeeNavigationFields
     * @return EmployeeNavigation
     */
    public function makeEmployeeNavigation($employeeNavigationFields = [])
    {
        /** @var EmployeeNavigationRepository $employeeNavigationRepo */
        $employeeNavigationRepo = App::make(EmployeeNavigationRepository::class);
        $theme = $this->fakeEmployeeNavigationData($employeeNavigationFields);
        return $employeeNavigationRepo->create($theme);
    }

    /**
     * Get fake instance of EmployeeNavigation
     *
     * @param array $employeeNavigationFields
     * @return EmployeeNavigation
     */
    public function fakeEmployeeNavigation($employeeNavigationFields = [])
    {
        return new EmployeeNavigation($this->fakeEmployeeNavigationData($employeeNavigationFields));
    }

    /**
     * Get fake data of EmployeeNavigation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEmployeeNavigationData($employeeNavigationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'empID' => $fake->word,
            'userGroupID' => $fake->randomDigitNotNull,
            'companyID' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $employeeNavigationFields);
    }
}
