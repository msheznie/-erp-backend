<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\SecondaryCompany;
use App\Repositories\SecondaryCompanyRepository;

trait MakeSecondaryCompanyTrait
{
    /**
     * Create fake instance of SecondaryCompany and save it in database
     *
     * @param array $secondaryCompanyFields
     * @return SecondaryCompany
     */
    public function makeSecondaryCompany($secondaryCompanyFields = [])
    {
        /** @var SecondaryCompanyRepository $secondaryCompanyRepo */
        $secondaryCompanyRepo = \App::make(SecondaryCompanyRepository::class);
        $theme = $this->fakeSecondaryCompanyData($secondaryCompanyFields);
        return $secondaryCompanyRepo->create($theme);
    }

    /**
     * Get fake instance of SecondaryCompany
     *
     * @param array $secondaryCompanyFields
     * @return SecondaryCompany
     */
    public function fakeSecondaryCompany($secondaryCompanyFields = [])
    {
        return new SecondaryCompany($this->fakeSecondaryCompanyData($secondaryCompanyFields));
    }

    /**
     * Get fake data of SecondaryCompany
     *
     * @param array $secondaryCompanyFields
     * @return array
     */
    public function fakeSecondaryCompanyData($secondaryCompanyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'logo' => $fake->word,
            'name' => $fake->word
        ], $secondaryCompanyFields);
    }
}
