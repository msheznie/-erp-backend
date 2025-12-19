<?php

use Faker\Factory as Faker;
use App\Models\CompanyPolicyMaster;
use App\Repositories\CompanyPolicyMasterRepository;

trait MakeCompanyPolicyMasterTrait
{
    /**
     * Create fake instance of CompanyPolicyMaster and save it in database
     *
     * @param array $companyPolicyMasterFields
     * @return CompanyPolicyMaster
     */
    public function makeCompanyPolicyMaster($companyPolicyMasterFields = [])
    {
        /** @var CompanyPolicyMasterRepository $companyPolicyMasterRepo */
        $companyPolicyMasterRepo = App::make(CompanyPolicyMasterRepository::class);
        $theme = $this->fakeCompanyPolicyMasterData($companyPolicyMasterFields);
        return $companyPolicyMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyPolicyMaster
     *
     * @param array $companyPolicyMasterFields
     * @return CompanyPolicyMaster
     */
    public function fakeCompanyPolicyMaster($companyPolicyMasterFields = [])
    {
        return new CompanyPolicyMaster($this->fakeCompanyPolicyMasterData($companyPolicyMasterFields));
    }

    /**
     * Get fake data of CompanyPolicyMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyPolicyMasterData($companyPolicyMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyPolicyCategoryID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentID' => $fake->word,
            'isYesNO' => $fake->randomDigitNotNull,
            'policyValue' => $fake->randomDigitNotNull,
            'createdByUserID' => $fake->word,
            'createdByUserName' => $fake->word,
            'createdByPCID' => $fake->word,
            'modifiedByUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $companyPolicyMasterFields);
    }
}
