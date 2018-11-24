<?php

use Faker\Factory as Faker;
use App\Models\CustomerReceivePaymentRefferedHistory;
use App\Repositories\CustomerReceivePaymentRefferedHistoryRepository;

trait MakeCustomerReceivePaymentRefferedHistoryTrait
{
    /**
     * Create fake instance of CustomerReceivePaymentRefferedHistory and save it in database
     *
     * @param array $customerReceivePaymentRefferedHistoryFields
     * @return CustomerReceivePaymentRefferedHistory
     */
    public function makeCustomerReceivePaymentRefferedHistory($customerReceivePaymentRefferedHistoryFields = [])
    {
        /** @var CustomerReceivePaymentRefferedHistoryRepository $customerReceivePaymentRefferedHistoryRepo */
        $customerReceivePaymentRefferedHistoryRepo = App::make(CustomerReceivePaymentRefferedHistoryRepository::class);
        $theme = $this->fakeCustomerReceivePaymentRefferedHistoryData($customerReceivePaymentRefferedHistoryFields);
        return $customerReceivePaymentRefferedHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerReceivePaymentRefferedHistory
     *
     * @param array $customerReceivePaymentRefferedHistoryFields
     * @return CustomerReceivePaymentRefferedHistory
     */
    public function fakeCustomerReceivePaymentRefferedHistory($customerReceivePaymentRefferedHistoryFields = [])
    {
        return new CustomerReceivePaymentRefferedHistory($this->fakeCustomerReceivePaymentRefferedHistoryData($customerReceivePaymentRefferedHistoryFields));
    }

    /**
     * Get fake data of CustomerReceivePaymentRefferedHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerReceivePaymentRefferedHistoryData($customerReceivePaymentRefferedHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custReceivePaymentAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'intercompanyPaymentID' => $fake->randomDigitNotNull,
            'intercompanyPaymentCode' => $fake->word,
            'custPaymentReceiveCode' => $fake->word,
            'custPaymentReceiveDate' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'customerGLCodeSystemID' => $fake->randomDigitNotNull,
            'customerGLCode' => $fake->word,
            'custTransactionCurrencyID' => $fake->randomDigitNotNull,
            'custTransactionCurrencyER' => $fake->randomDigitNotNull,
            'bankID' => $fake->randomDigitNotNull,
            'bankAccount' => $fake->randomDigitNotNull,
            'bankCurrency' => $fake->randomDigitNotNull,
            'bankCurrencyER' => $fake->randomDigitNotNull,
            'payeeYN' => $fake->randomDigitNotNull,
            'PayeeSelectEmp' => $fake->randomDigitNotNull,
            'PayeeEmpID' => $fake->word,
            'PayeeName' => $fake->word,
            'PayeeCurrency' => $fake->randomDigitNotNull,
            'custChequeNo' => $fake->randomDigitNotNull,
            'custChequeDate' => $fake->date('Y-m-d H:i:s'),
            'custChequeBank' => $fake->word,
            'receivedAmount' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'companyRptCurrencyID' => $fake->randomDigitNotNull,
            'companyRptCurrencyER' => $fake->randomDigitNotNull,
            'companyRptAmount' => $fake->randomDigitNotNull,
            'bankAmount' => $fake->randomDigitNotNull,
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
            'trsCollectedYN' => $fake->randomDigitNotNull,
            'trsCollectedByEmpSystemID' => $fake->randomDigitNotNull,
            'trsCollectedByEmpID' => $fake->word,
            'trsCollectedByEmpName' => $fake->word,
            'trsCollectedDate' => $fake->date('Y-m-d H:i:s'),
            'trsClearedYN' => $fake->randomDigitNotNull,
            'trsClearedDate' => $fake->date('Y-m-d H:i:s'),
            'trsClearedByEmpSystemID' => $fake->randomDigitNotNull,
            'trsClearedByEmpID' => $fake->word,
            'trsClearedByEmpName' => $fake->word,
            'trsClearedAmount' => $fake->randomDigitNotNull,
            'bankClearedYN' => $fake->randomDigitNotNull,
            'bankClearedAmount' => $fake->randomDigitNotNull,
            'bankReconciliationDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedByEmpSystemID' => $fake->randomDigitNotNull,
            'bankClearedByEmpID' => $fake->word,
            'bankClearedByEmpName' => $fake->word,
            'documentType' => $fake->randomDigitNotNull,
            'matchInvoice' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'expenseClaimOrPettyCash' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerReceivePaymentRefferedHistoryFields);
    }
}
