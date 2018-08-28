<?php

use Faker\Factory as Faker;
use App\Models\CustomerReceivePayment;
use App\Repositories\CustomerReceivePaymentRepository;

trait MakeCustomerReceivePaymentTrait
{
    /**
     * Create fake instance of CustomerReceivePayment and save it in database
     *
     * @param array $customerReceivePaymentFields
     * @return CustomerReceivePayment
     */
    public function makeCustomerReceivePayment($customerReceivePaymentFields = [])
    {
        /** @var CustomerReceivePaymentRepository $customerReceivePaymentRepo */
        $customerReceivePaymentRepo = App::make(CustomerReceivePaymentRepository::class);
        $theme = $this->fakeCustomerReceivePaymentData($customerReceivePaymentFields);
        return $customerReceivePaymentRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerReceivePayment
     *
     * @param array $customerReceivePaymentFields
     * @return CustomerReceivePayment
     */
    public function fakeCustomerReceivePayment($customerReceivePaymentFields = [])
    {
        return new CustomerReceivePayment($this->fakeCustomerReceivePaymentData($customerReceivePaymentFields));
    }

    /**
     * Get fake data of CustomerReceivePayment
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerReceivePaymentData($customerReceivePaymentFields = [])
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
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
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
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'trsCollectedYN' => $fake->randomDigitNotNull,
            'trsCollectedByEmpID' => $fake->word,
            'trsCollectedByEmpName' => $fake->word,
            'trsCollectedDate' => $fake->date('Y-m-d H:i:s'),
            'trsClearedYN' => $fake->randomDigitNotNull,
            'trsClearedDate' => $fake->date('Y-m-d H:i:s'),
            'trsClearedByEmpID' => $fake->word,
            'trsClearedByEmpName' => $fake->word,
            'trsClearedAmount' => $fake->randomDigitNotNull,
            'bankClearedYN' => $fake->randomDigitNotNull,
            'bankClearedAmount' => $fake->randomDigitNotNull,
            'bankReconciliationDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedByEmpID' => $fake->word,
            'bankClearedByEmpName' => $fake->word,
            'documentType' => $fake->randomDigitNotNull,
            'matchInvoice' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'expenseClaimOrPettyCash' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerReceivePaymentFields);
    }
}
