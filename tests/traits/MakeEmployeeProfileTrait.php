<?php

use Faker\Factory as Faker;
use App\Models\EmployeeProfile;
use App\Repositories\EmployeeProfileRepository;

trait MakeEmployeeProfileTrait
{
    /**
     * Create fake instance of EmployeeProfile and save it in database
     *
     * @param array $employeeProfileFields
     * @return EmployeeProfile
     */
    public function makeEmployeeProfile($employeeProfileFields = [])
    {
        /** @var EmployeeProfileRepository $employeeProfileRepo */
        $employeeProfileRepo = App::make(EmployeeProfileRepository::class);
        $theme = $this->fakeEmployeeProfileData($employeeProfileFields);
        return $employeeProfileRepo->create($theme);
    }

    /**
     * Get fake instance of EmployeeProfile
     *
     * @param array $employeeProfileFields
     * @return EmployeeProfile
     */
    public function fakeEmployeeProfile($employeeProfileFields = [])
    {
        return new EmployeeProfile($this->fakeEmployeeProfileData($employeeProfileFields));
    }

    /**
     * Get fake data of EmployeeProfile
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEmployeeProfileData($employeeProfileFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'employeeSystemID' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'profileImage' => $fake->word,
            'modifiedDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $employeeProfileFields);
    }
}
