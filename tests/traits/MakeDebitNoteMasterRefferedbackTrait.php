<?php

use Faker\Factory as Faker;
use App\Models\DebitNoteMasterRefferedback;
use App\Repositories\DebitNoteMasterRefferedbackRepository;

trait MakeDebitNoteMasterRefferedbackTrait
{
    /**
     * Create fake instance of DebitNoteMasterRefferedback and save it in database
     *
     * @param array $debitNoteMasterRefferedbackFields
     * @return DebitNoteMasterRefferedback
     */
    public function makeDebitNoteMasterRefferedback($debitNoteMasterRefferedbackFields = [])
    {
        /** @var DebitNoteMasterRefferedbackRepository $debitNoteMasterRefferedbackRepo */
        $debitNoteMasterRefferedbackRepo = App::make(DebitNoteMasterRefferedbackRepository::class);
        $theme = $this->fakeDebitNoteMasterRefferedbackData($debitNoteMasterRefferedbackFields);
        return $debitNoteMasterRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of DebitNoteMasterRefferedback
     *
     * @param array $debitNoteMasterRefferedbackFields
     * @return DebitNoteMasterRefferedback
     */
    public function fakeDebitNoteMasterRefferedback($debitNoteMasterRefferedbackFields = [])
    {
        return new DebitNoteMasterRefferedback($this->fakeDebitNoteMasterRefferedbackData($debitNoteMasterRefferedbackFields));
    }

    /**
     * Get fake data of DebitNoteMasterRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDebitNoteMasterRefferedbackData($debitNoteMasterRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'debitNoteAutoID' => $fake->randomDigitNotNull,
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
            'referenceNumber' => $fake->word,
            'invoiceNumber' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'supplierGLCodeSystemID' => $fake->randomDigitNotNull,
            'supplierGLCode' => $fake->word,
            'liabilityAccountSysemID' => $fake->randomDigitNotNull,
            'liabilityAccount' => $fake->word,
            'UnbilledGRVAccountSystemID' => $fake->randomDigitNotNull,
            'UnbilledGRVAccount' => $fake->word,
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
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'documentType' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
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
            'createdDateAndTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $debitNoteMasterRefferedbackFields);
    }
}
