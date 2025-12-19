<?php

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceDirectRefferedback;
use App\Repositories\CustomerInvoiceDirectRefferedbackRepository;

trait MakeCustomerInvoiceDirectRefferedbackTrait
{
    /**
     * Create fake instance of CustomerInvoiceDirectRefferedback and save it in database
     *
     * @param array $customerInvoiceDirectRefferedbackFields
     * @return CustomerInvoiceDirectRefferedback
     */
    public function makeCustomerInvoiceDirectRefferedback($customerInvoiceDirectRefferedbackFields = [])
    {
        /** @var CustomerInvoiceDirectRefferedbackRepository $customerInvoiceDirectRefferedbackRepo */
        $customerInvoiceDirectRefferedbackRepo = App::make(CustomerInvoiceDirectRefferedbackRepository::class);
        $theme = $this->fakeCustomerInvoiceDirectRefferedbackData($customerInvoiceDirectRefferedbackFields);
        return $customerInvoiceDirectRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceDirectRefferedback
     *
     * @param array $customerInvoiceDirectRefferedbackFields
     * @return CustomerInvoiceDirectRefferedback
     */
    public function fakeCustomerInvoiceDirectRefferedback($customerInvoiceDirectRefferedbackFields = [])
    {
        return new CustomerInvoiceDirectRefferedback($this->fakeCustomerInvoiceDirectRefferedbackData($customerInvoiceDirectRefferedbackFields));
    }

    /**
     * Get fake data of CustomerInvoiceDirectRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerInvoiceDirectRefferedbackData($customerInvoiceDirectRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custInvoiceDirectAutoID' => $fake->randomDigitNotNull,
            'transactionMode' => $fake->randomDigitNotNull,
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
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'wareHouseSystemCode' => $fake->randomDigitNotNull,
            'bookingInvCode' => $fake->word,
            'bookingDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->word,
            'invoiceDueDate' => $fake->date('Y-m-d H:i:s'),
            'customerGRVAutoID' => $fake->randomDigitNotNull,
            'bankID' => $fake->randomDigitNotNull,
            'bankAccountID' => $fake->randomDigitNotNull,
            'performaDate' => $fake->date('Y-m-d H:i:s'),
            'wanNO' => $fake->randomDigitNotNull,
            'PONumber' => $fake->word,
            'rigNo' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'customerGLCode' => $fake->word,
            'customerGLSystemID' => $fake->randomDigitNotNull,
            'customerInvoiceNo' => $fake->word,
            'customerInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'custTransactionCurrencyID' => $fake->randomDigitNotNull,
            'custTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'bookingAmountTrans' => $fake->randomDigitNotNull,
            'bookingAmountLocal' => $fake->randomDigitNotNull,
            'bookingAmountRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'servicePeriod' => $fake->word,
            'paymentInDaysForJob' => $fake->randomDigitNotNull,
            'serviceStartDate' => $fake->date('Y-m-d H:i:s'),
            'serviceEndDate' => $fake->date('Y-m-d H:i:s'),
            'isPerforma' => $fake->randomDigitNotNull,
            'documentType' => $fake->randomDigitNotNull,
            'secondaryLogoCompanySystemID' => $fake->randomDigitNotNull,
            'secondaryLogoCompID' => $fake->word,
            'secondaryLogo' => $fake->word,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'selectedForTracking' => $fake->randomDigitNotNull,
            'customerInvoiceTrackingID' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'canceledByEmpSystemID' => $fake->randomDigitNotNull,
            'canceledYN' => $fake->randomDigitNotNull,
            'canceledByEmpID' => $fake->word,
            'canceledByEmpName' => $fake->word,
            'vatOutputGLCodeSystemID' => $fake->randomDigitNotNull,
            'vatOutputGLCode' => $fake->word,
            'VATPercentage' => $fake->randomDigitNotNull,
            'VATAmount' => $fake->randomDigitNotNull,
            'VATAmountLocal' => $fake->randomDigitNotNull,
            'VATAmountRpt' => $fake->randomDigitNotNull,
            'discountLocalAmount' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'discountRptAmount' => $fake->randomDigitNotNull,
            'canceledDateTime' => $fake->date('Y-m-d H:i:s'),
            'canceledComments' => $fake->text,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'createdDateAndTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->randomDigitNotNull,
            'approvedByUserSystemID' => $fake->randomDigitNotNull
        ], $customerInvoiceDirectRefferedbackFields);
    }
}
