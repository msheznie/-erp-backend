<?php

use Faker\Factory as Faker;
use App\Models\CompanyPolicyCategory;
use App\Repositories\CompanyPolicyCategoryRepository;

trait MakeCompanyPolicyCategoryTrait
{
    /**
     * Create fake instance of CompanyPolicyCategory and save it in database
     *
     * @param array $companyPolicyCategoryFields
     * @return CompanyPolicyCategory
     */
    public function makeCompanyPolicyCategory($companyPolicyCategoryFields = [])
    {
        /** @var CompanyPolicyCategoryRepository $companyPolicyCategoryRepo */
        $companyPolicyCategoryRepo = App::make(CompanyPolicyCategoryRepository::class);
        $theme = $this->fakeCompanyPolicyCategoryData($companyPolicyCategoryFields);
        return $companyPolicyCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyPolicyCategory
     *
     * @param array $companyPolicyCategoryFields
     * @return CompanyPolicyCategory
     */
    public function fakeCompanyPolicyCategory($companyPolicyCategoryFields = [])
    {
        return new CompanyPolicyCategory($this->fakeCompanyPolicyCategoryData($companyPolicyCategoryFields));
    }

    /**
     * Get fake data of CompanyPolicyCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyPolicyCategoryData($companyPolicyCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyPolicyCategoryDescription' => $fake->word,
            'applicableDocumentID' => $fake->word,
            'documentID' => $fake->word,
            'impletemed' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'timestamp' => $fake->word
        ], $companyPolicyCategoryFields);
    }
}
