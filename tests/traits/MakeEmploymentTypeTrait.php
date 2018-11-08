<?php

use Faker\Factory as Faker;
use App\Models\EmploymentType;
use App\Repositories\EmploymentTypeRepository;

trait MakeEmploymentTypeTrait
{
    /**
     * Create fake instance of EmploymentType and save it in database
     *
     * @param array $employmentTypeFields
     * @return EmploymentType
     */
    public function makeEmploymentType($employmentTypeFields = [])
    {
        /** @var EmploymentTypeRepository $employmentTypeRepo */
        $employmentTypeRepo = App::make(EmploymentTypeRepository::class);
        $theme = $this->fakeEmploymentTypeData($employmentTypeFields);
        return $employmentTypeRepo->create($theme);
    }

    /**
     * Get fake instance of EmploymentType
     *
     * @param array $employmentTypeFields
     * @return EmploymentType
     */
    public function fakeEmploymentType($employmentTypeFields = [])
    {
        return new EmploymentType($this->fakeEmploymentTypeData($employmentTypeFields));
    }

    /**
     * Get fake data of EmploymentType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEmploymentTypeData($employmentTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word
        ], $employmentTypeFields);
    }
}
