<?php

use Faker\Factory as Faker;
use App\Models\TaxAuthority;
use App\Repositories\TaxAuthorityRepository;

trait MakeTaxAuthorityTrait
{
    /**
     * Create fake instance of TaxAuthority and save it in database
     *
     * @param array $taxAuthorityFields
     * @return TaxAuthority
     */
    public function makeTaxAuthority($taxAuthorityFields = [])
    {
        /** @var TaxAuthorityRepository $taxAuthorityRepo */
        $taxAuthorityRepo = App::make(TaxAuthorityRepository::class);
        $theme = $this->fakeTaxAuthorityData($taxAuthorityFields);
        return $taxAuthorityRepo->create($theme);
    }

    /**
     * Get fake instance of TaxAuthority
     *
     * @param array $taxAuthorityFields
     * @return TaxAuthority
     */
    public function fakeTaxAuthority($taxAuthorityFields = [])
    {
        return new TaxAuthority($this->fakeTaxAuthorityData($taxAuthorityFields));
    }

    /**
     * Get fake data of TaxAuthority
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTaxAuthorityData($taxAuthorityFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'authoritySystemCode' => $fake->word,
            'authoritySecondaryCode' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'AuthorityName' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'telephone' => $fake->word,
            'email' => $fake->word,
            'fax' => $fake->word,
            'address' => $fake->text,
            'taxPayableGLAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdUserName' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedUserName' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $taxAuthorityFields);
    }
}
