<?php

use Faker\Factory as Faker;
use App\Models\DebitNoteDetails;
use App\Repositories\DebitNoteDetailsRepository;

trait MakeDebitNoteDetailsTrait
{
    /**
     * Create fake instance of DebitNoteDetails and save it in database
     *
     * @param array $debitNoteDetailsFields
     * @return DebitNoteDetails
     */
    public function makeDebitNoteDetails($debitNoteDetailsFields = [])
    {
        /** @var DebitNoteDetailsRepository $debitNoteDetailsRepo */
        $debitNoteDetailsRepo = App::make(DebitNoteDetailsRepository::class);
        $theme = $this->fakeDebitNoteDetailsData($debitNoteDetailsFields);
        return $debitNoteDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of DebitNoteDetails
     *
     * @param array $debitNoteDetailsFields
     * @return DebitNoteDetails
     */
    public function fakeDebitNoteDetails($debitNoteDetailsFields = [])
    {
        return new DebitNoteDetails($this->fakeDebitNoteDetailsData($debitNoteDetailsFields));
    }

    /**
     * Get fake data of DebitNoteDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDebitNoteDetailsData($debitNoteDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'debitNoteAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'contractID' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'comments' => $fake->word,
            'debitAmountCurrency' => $fake->randomDigitNotNull,
            'debitAmountCurrencyER' => $fake->randomDigitNotNull,
            'debitAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $debitNoteDetailsFields);
    }
}
