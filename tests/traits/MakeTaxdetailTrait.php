<?php

use Faker\Factory as Faker;
use App\Models\Taxdetail;
use App\Repositories\TaxdetailRepository;

trait MakeTaxdetailTrait
{
    /**
     * Create fake instance of Taxdetail and save it in database
     *
     * @param array $taxdetailFields
     * @return Taxdetail
     */
    public function makeTaxdetail($taxdetailFields = [])
    {
        /** @var TaxdetailRepository $taxdetailRepo */
        $taxdetailRepo = App::make(TaxdetailRepository::class);
        $theme = $this->fakeTaxdetailData($taxdetailFields);
        return $taxdetailRepo->create($theme);
    }

    /**
     * Get fake instance of Taxdetail
     *
     * @param array $taxdetailFields
     * @return Taxdetail
     */
    public function fakeTaxdetail($taxdetailFields = [])
    {
        return new Taxdetail($this->fakeTaxdetailData($taxdetailFields));
    }

    /**
     * Get fake data of Taxdetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTaxdetailData($taxdetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'taxMasterAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'taxShortCode' => $fake->word,
            'taxDescription' => $fake->word,
            'taxPercent' => $fake->randomDigitNotNull,
            'payeeSystemCode' => $fake->randomDigitNotNull,
            'payeeCode' => $fake->word,
            'payeeName' => $fake->word,
            'currency' => $fake->randomDigitNotNull,
            'currencyER' => $fake->randomDigitNotNull,
            'amount' => $fake->randomDigitNotNull,
            'payeeDefaultCurrencyID' => $fake->randomDigitNotNull,
            'payeeDefaultCurrencyER' => $fake->randomDigitNotNull,
            'payeeDefaultAmount' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyER' => $fake->randomDigitNotNull,
            'rptAmount' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $taxdetailFields);
    }
}
