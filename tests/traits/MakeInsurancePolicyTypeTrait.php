<?php

use Faker\Factory as Faker;
use App\Models\InsurancePolicyType;
use App\Repositories\InsurancePolicyTypeRepository;

trait MakeInsurancePolicyTypeTrait
{
    /**
     * Create fake instance of InsurancePolicyType and save it in database
     *
     * @param array $insurancePolicyTypeFields
     * @return InsurancePolicyType
     */
    public function makeInsurancePolicyType($insurancePolicyTypeFields = [])
    {
        /** @var InsurancePolicyTypeRepository $insurancePolicyTypeRepo */
        $insurancePolicyTypeRepo = App::make(InsurancePolicyTypeRepository::class);
        $theme = $this->fakeInsurancePolicyTypeData($insurancePolicyTypeFields);
        return $insurancePolicyTypeRepo->create($theme);
    }

    /**
     * Get fake instance of InsurancePolicyType
     *
     * @param array $insurancePolicyTypeFields
     * @return InsurancePolicyType
     */
    public function fakeInsurancePolicyType($insurancePolicyTypeFields = [])
    {
        return new InsurancePolicyType($this->fakeInsurancePolicyTypeData($insurancePolicyTypeFields));
    }

    /**
     * Get fake data of InsurancePolicyType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeInsurancePolicyTypeData($insurancePolicyTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'policyDescription' => $fake->word
        ], $insurancePolicyTypeFields);
    }
}
