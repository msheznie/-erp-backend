<?php

use Faker\Factory as Faker;
use App\Models\Tax;
use App\Repositories\TaxRepository;

trait MakeTaxTrait
{
    /**
     * Create fake instance of Tax and save it in database
     *
     * @param array $taxFields
     * @return Tax
     */
    public function makeTax($taxFields = [])
    {
        /** @var TaxRepository $taxRepo */
        $taxRepo = App::make(TaxRepository::class);
        $theme = $this->fakeTaxData($taxFields);
        return $taxRepo->create($theme);
    }

    /**
     * Get fake instance of Tax
     *
     * @param array $taxFields
     * @return Tax
     */
    public function fakeTax($taxFields = [])
    {
        return new Tax($this->fakeTaxData($taxFields));
    }

    /**
     * Get fake data of Tax
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTaxData($taxFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'taxDescription' => $fake->word,
            'taxShortCode' => $fake->word,
            'taxType' => $fake->word,
            'isActive' => $fake->word,
            'authorityAutoID' => $fake->randomDigitNotNull,
            'GLAutoID' => $fake->randomDigitNotNull,
            'currencyID' => $fake->randomDigitNotNull,
            'effectiveFrom' => $fake->word,
            'taxReferenceNo' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $taxFields);
    }
}
