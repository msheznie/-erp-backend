<?php

use Faker\Factory as Faker;
use App\Models\SalaryProcessEmploymentTypes;
use App\Repositories\SalaryProcessEmploymentTypesRepository;

trait MakeSalaryProcessEmploymentTypesTrait
{
    /**
     * Create fake instance of SalaryProcessEmploymentTypes and save it in database
     *
     * @param array $salaryProcessEmploymentTypesFields
     * @return SalaryProcessEmploymentTypes
     */
    public function makeSalaryProcessEmploymentTypes($salaryProcessEmploymentTypesFields = [])
    {
        /** @var SalaryProcessEmploymentTypesRepository $salaryProcessEmploymentTypesRepo */
        $salaryProcessEmploymentTypesRepo = App::make(SalaryProcessEmploymentTypesRepository::class);
        $theme = $this->fakeSalaryProcessEmploymentTypesData($salaryProcessEmploymentTypesFields);
        return $salaryProcessEmploymentTypesRepo->create($theme);
    }

    /**
     * Get fake instance of SalaryProcessEmploymentTypes
     *
     * @param array $salaryProcessEmploymentTypesFields
     * @return SalaryProcessEmploymentTypes
     */
    public function fakeSalaryProcessEmploymentTypes($salaryProcessEmploymentTypesFields = [])
    {
        return new SalaryProcessEmploymentTypes($this->fakeSalaryProcessEmploymentTypesData($salaryProcessEmploymentTypesFields));
    }

    /**
     * Get fake data of SalaryProcessEmploymentTypes
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSalaryProcessEmploymentTypesData($salaryProcessEmploymentTypesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'salaryProcessID' => $fake->randomDigitNotNull,
            'empType' => $fake->randomDigitNotNull,
            'periodID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $salaryProcessEmploymentTypesFields);
    }
}
