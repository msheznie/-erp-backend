<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\EmployeeManagers;
use App\Repositories\EmployeeManagersRepository;

trait MakeEmployeeManagersTrait
{
    /**
     * Create fake instance of EmployeeManagers and save it in database
     *
     * @param array $employeeManagersFields
     * @return EmployeeManagers
     */
    public function makeEmployeeManagers($employeeManagersFields = [])
    {
        /** @var EmployeeManagersRepository $employeeManagersRepo */
        $employeeManagersRepo = \App::make(EmployeeManagersRepository::class);
        $theme = $this->fakeEmployeeManagersData($employeeManagersFields);
        return $employeeManagersRepo->create($theme);
    }

    /**
     * Get fake instance of EmployeeManagers
     *
     * @param array $employeeManagersFields
     * @return EmployeeManagers
     */
    public function fakeEmployeeManagers($employeeManagersFields = [])
    {
        return new EmployeeManagers($this->fakeEmployeeManagersData($employeeManagersFields));
    }

    /**
     * Get fake data of EmployeeManagers
     *
     * @param array $employeeManagersFields
     * @return array
     */
    public function fakeEmployeeManagersData($employeeManagersFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'empID' => $fake->word,
            'managerID' => $fake->word,
            'level' => $fake->randomDigitNotNull,
            'isFunctionalManager' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDate' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserID' => $fake->word,
            'modifiedDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $employeeManagersFields);
    }
}
