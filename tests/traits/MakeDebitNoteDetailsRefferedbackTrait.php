<?php

use Faker\Factory as Faker;
use App\Models\DebitNoteDetailsRefferedback;
use App\Repositories\DebitNoteDetailsRefferedbackRepository;

trait MakeDebitNoteDetailsRefferedbackTrait
{
    /**
     * Create fake instance of DebitNoteDetailsRefferedback and save it in database
     *
     * @param array $debitNoteDetailsRefferedbackFields
     * @return DebitNoteDetailsRefferedback
     */
    public function makeDebitNoteDetailsRefferedback($debitNoteDetailsRefferedbackFields = [])
    {
        /** @var DebitNoteDetailsRefferedbackRepository $debitNoteDetailsRefferedbackRepo */
        $debitNoteDetailsRefferedbackRepo = App::make(DebitNoteDetailsRefferedbackRepository::class);
        $theme = $this->fakeDebitNoteDetailsRefferedbackData($debitNoteDetailsRefferedbackFields);
        return $debitNoteDetailsRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of DebitNoteDetailsRefferedback
     *
     * @param array $debitNoteDetailsRefferedbackFields
     * @return DebitNoteDetailsRefferedback
     */
    public function fakeDebitNoteDetailsRefferedback($debitNoteDetailsRefferedbackFields = [])
    {
        return new DebitNoteDetailsRefferedback($this->fakeDebitNoteDetailsRefferedbackData($debitNoteDetailsRefferedbackFields));
    }

    /**
     * Get fake data of DebitNoteDetailsRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDebitNoteDetailsRefferedbackData($debitNoteDetailsRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'debitNoteDetailsID' => $fake->randomDigitNotNull,
            'debitNoteAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'contractID' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
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
        ], $debitNoteDetailsRefferedbackFields);
    }
}
