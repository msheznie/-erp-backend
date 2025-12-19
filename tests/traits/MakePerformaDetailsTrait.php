<?php

use Faker\Factory as Faker;
use App\Models\PerformaDetails;
use App\Repositories\PerformaDetailsRepository;

trait MakePerformaDetailsTrait
{
    /**
     * Create fake instance of PerformaDetails and save it in database
     *
     * @param array $performaDetailsFields
     * @return PerformaDetails
     */
    public function makePerformaDetails($performaDetailsFields = [])
    {
        /** @var PerformaDetailsRepository $performaDetailsRepo */
        $performaDetailsRepo = App::make(PerformaDetailsRepository::class);
        $theme = $this->fakePerformaDetailsData($performaDetailsFields);
        return $performaDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of PerformaDetails
     *
     * @param array $performaDetailsFields
     * @return PerformaDetails
     */
    public function fakePerformaDetails($performaDetailsFields = [])
    {
        return new PerformaDetails($this->fakePerformaDetailsData($performaDetailsFields));
    }

    /**
     * Get fake data of PerformaDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakePerformaDetailsData($performaDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'serviceLine' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'contractID' => $fake->word,
            'performaMasterID' => $fake->randomDigitNotNull,
            'performaCode' => $fake->word,
            'ticketNo' => $fake->randomDigitNotNull,
            'currencyID' => $fake->randomDigitNotNull,
            'totAmount' => $fake->randomDigitNotNull,
            'financeGLcode' => $fake->word,
            'invoiceSsytemCode' => $fake->randomDigitNotNull,
            'vendorCode' => $fake->word,
            'bankID' => $fake->randomDigitNotNull,
            'accountID' => $fake->randomDigitNotNull,
            'paymentPeriodDays' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $performaDetailsFields);
    }
}
