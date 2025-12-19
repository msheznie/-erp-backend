<?php

use Faker\Factory as Faker;
use App\Models\CreditNote;
use App\Repositories\CreditNoteRepository;

trait MakeCreditNoteTrait
{
    /**
     * Create fake instance of CreditNote and save it in database
     *
     * @param array $creditNoteFields
     * @return CreditNote
     */
    public function makeCreditNote($creditNoteFields = [])
    {
        /** @var CreditNoteRepository $creditNoteRepo */
        $creditNoteRepo = App::make(CreditNoteRepository::class);
        $theme = $this->fakeCreditNoteData($creditNoteFields);
        return $creditNoteRepo->create($theme);
    }

    /**
     * Get fake instance of CreditNote
     *
     * @param array $creditNoteFields
     * @return CreditNote
     */
    public function fakeCreditNote($creditNoteFields = [])
    {
        return new CreditNote($this->fakeCreditNoteData($creditNoteFields));
    }

    /**
     * Get fake data of CreditNote
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCreditNoteData($creditNoteFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemiD' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'creditNoteCode' => $fake->word,
            'creditNoteDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'customerGLCodeSystemID' => $fake->randomDigitNotNull,
            'customerGLCode' => $fake->word,
            'customerCurrencyID' => $fake->randomDigitNotNull,
            'customerCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'creditAmountTrans' => $fake->randomDigitNotNull,
            'creditAmountLocal' => $fake->randomDigitNotNull,
            'creditAmountRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'secondaryLogoCompID' => $fake->word,
            'secondaryLogo' => $fake->word,
            'matchInvoice' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'documentType' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $creditNoteFields);
    }
}
