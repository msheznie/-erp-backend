<?php

use Faker\Factory as Faker;
use App\Models\CreditNoteReferredback;
use App\Repositories\CreditNoteReferredbackRepository;

trait MakeCreditNoteReferredbackTrait
{
    /**
     * Create fake instance of CreditNoteReferredback and save it in database
     *
     * @param array $creditNoteReferredbackFields
     * @return CreditNoteReferredback
     */
    public function makeCreditNoteReferredback($creditNoteReferredbackFields = [])
    {
        /** @var CreditNoteReferredbackRepository $creditNoteReferredbackRepo */
        $creditNoteReferredbackRepo = App::make(CreditNoteReferredbackRepository::class);
        $theme = $this->fakeCreditNoteReferredbackData($creditNoteReferredbackFields);
        return $creditNoteReferredbackRepo->create($theme);
    }

    /**
     * Get fake instance of CreditNoteReferredback
     *
     * @param array $creditNoteReferredbackFields
     * @return CreditNoteReferredback
     */
    public function fakeCreditNoteReferredback($creditNoteReferredbackFields = [])
    {
        return new CreditNoteReferredback($this->fakeCreditNoteReferredbackData($creditNoteReferredbackFields));
    }

    /**
     * Get fake data of CreditNoteReferredback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCreditNoteReferredbackData($creditNoteReferredbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'creditNoteAutoID' => $fake->randomDigitNotNull,
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
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'secondaryLogoCompanySystemID' => $fake->randomDigitNotNull,
            'secondaryLogoCompID' => $fake->word,
            'secondaryLogo' => $fake->word,
            'matchInvoice' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'documentType' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
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
            'createdDateAndTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $creditNoteReferredbackFields);
    }
}
