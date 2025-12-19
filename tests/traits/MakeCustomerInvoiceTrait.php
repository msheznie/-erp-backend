<?php

use Faker\Factory as Faker;
use App\Models\CustomerInvoice;
use App\Repositories\CustomerInvoiceRepository;

trait MakeCustomerInvoiceTrait
{
    /**
     * Create fake instance of CustomerInvoice and save it in database
     *
     * @param array $customerInvoiceFields
     * @return CustomerInvoice
     */
    public function makeCustomerInvoice($customerInvoiceFields = [])
    {
        /** @var CustomerInvoiceRepository $customerInvoiceRepo */
        $customerInvoiceRepo = App::make(CustomerInvoiceRepository::class);
        $theme = $this->fakeCustomerInvoiceData($customerInvoiceFields);
        return $customerInvoiceRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoice
     *
     * @param array $customerInvoiceFields
     * @return CustomerInvoice
     */
    public function fakeCustomerInvoice($customerInvoiceFields = [])
    {
        return new CustomerInvoice($this->fakeCustomerInvoiceData($customerInvoiceFields));
    }

    /**
     * Get fake data of CustomerInvoice
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerInvoiceData($customerInvoiceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'transactionMode' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
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
            'wanNO' => $fake->word,
            'PONumber' => $fake->word,
            'rigNo' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'customerGLCode' => $fake->word,
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
            'secondaryLogoCompID' => $fake->word,
            'secondaryLogo' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'selectedForTracking' => $fake->randomDigitNotNull,
            'customerInvoiceTrackingID' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'canceledYN' => $fake->randomDigitNotNull,
            'canceledByEmpID' => $fake->word,
            'canceledByEmpName' => $fake->word,
            'vatOutputGLCodeSystemID' => $fake->randomDigitNotNull,
            'vatOutputGLCode' => $fake->word,
            'VATPercentage' => $fake->randomDigitNotNull,
            'VATAmount' => $fake->randomDigitNotNull,
            'VATAmountLocal' => $fake->randomDigitNotNull,
            'VATAmountRpt' => $fake->randomDigitNotNull,
            'canceledDateTime' => $fake->date('Y-m-d H:i:s'),
            'canceledComments' => $fake->text,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'discountLocalAmount' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'discountRptAmount' => $fake->randomDigitNotNull
        ], $customerInvoiceFields);
    }
}
