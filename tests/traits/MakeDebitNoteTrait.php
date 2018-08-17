<?php

use Faker\Factory as Faker;
use App\Models\DebitNote;
use App\Repositories\DebitNoteRepository;

trait MakeDebitNoteTrait
{
    /**
     * Create fake instance of DebitNote and save it in database
     *
     * @param array $debitNoteFields
     * @return DebitNote
     */
    public function makeDebitNote($debitNoteFields = [])
    {
        /** @var DebitNoteRepository $debitNoteRepo */
        $debitNoteRepo = App::make(DebitNoteRepository::class);
        $theme = $this->fakeDebitNoteData($debitNoteFields);
        return $debitNoteRepo->create($theme);
    }

    /**
     * Get fake instance of DebitNote
     *
     * @param array $debitNoteFields
     * @return DebitNote
     */
    public function fakeDebitNote($debitNoteFields = [])
    {
        return new DebitNote($this->fakeDebitNoteData($debitNoteFields));
    }

    /**
     * Get fake data of DebitNote
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDebitNoteData($debitNoteFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'debitNoteCode' => $fake->word,
            'debitNoteDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'supplierGLCode' => $fake->word,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'debitAmountTrans' => $fake->randomDigitNotNull,
            'debitAmountLocal' => $fake->randomDigitNotNull,
            'debitAmountRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'documentType' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'matchInvoice' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $debitNoteFields);
    }
}
