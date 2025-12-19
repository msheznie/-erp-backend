<?php

use Faker\Factory as Faker;
use App\Models\CreditNoteDetails;
use App\Repositories\CreditNoteDetailsRepository;

trait MakeCreditNoteDetailsTrait
{
    /**
     * Create fake instance of CreditNoteDetails and save it in database
     *
     * @param array $creditNoteDetailsFields
     * @return CreditNoteDetails
     */
    public function makeCreditNoteDetails($creditNoteDetailsFields = [])
    {
        /** @var CreditNoteDetailsRepository $creditNoteDetailsRepo */
        $creditNoteDetailsRepo = App::make(CreditNoteDetailsRepository::class);
        $theme = $this->fakeCreditNoteDetailsData($creditNoteDetailsFields);
        return $creditNoteDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of CreditNoteDetails
     *
     * @param array $creditNoteDetailsFields
     * @return CreditNoteDetails
     */
    public function fakeCreditNoteDetails($creditNoteDetailsFields = [])
    {
        return new CreditNoteDetails($this->fakeCreditNoteDetailsData($creditNoteDetailsFields));
    }

    /**
     * Get fake data of CreditNoteDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCreditNoteDetailsData($creditNoteDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'creditNoteAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'serviceLineCode' => $fake->word,
            'clientContractID' => $fake->word,
            'comments' => $fake->text,
            'creditAmountCurrency' => $fake->randomDigitNotNull,
            'creditAmountCurrencyER' => $fake->randomDigitNotNull,
            'creditAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $creditNoteDetailsFields);
    }
}
