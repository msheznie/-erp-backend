<?php

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceDirect;
use App\Repositories\CustomerInvoiceDirectRepository;

trait MakeCustomerInvoiceDirectTrait
{
    /**
     * Create fake instance of CustomerInvoiceDirect and save it in database
     *
     * @param array $customerInvoiceDirectFields
     * @return CustomerInvoiceDirect
     */
    public function makeCustomerInvoiceDirect($customerInvoiceDirectFields = [])
    {
        /** @var CustomerInvoiceDirectRepository $customerInvoiceDirectRepo */
        $customerInvoiceDirectRepo = App::make(CustomerInvoiceDirectRepository::class);
        $theme = $this->fakeCustomerInvoiceDirectData($customerInvoiceDirectFields);
        return $customerInvoiceDirectRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceDirect
     *
     * @param array $customerInvoiceDirectFields
     * @return CustomerInvoiceDirect
     */
    public function fakeCustomerInvoiceDirect($customerInvoiceDirectFields = [])
    {
        return new CustomerInvoiceDirect($this->fakeCustomerInvoiceDirectData($customerInvoiceDirectFields));
    }

    /**
     * Get fake data of CustomerInvoiceDirect
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerInvoiceDirectData($customerInvoiceDirectFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'secondaryLogoCompID' => $fake->word,
            'secondaryLogo' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'selectedForTracking' => $fake->randomDigitNotNull,
            'customerInvoiceTrackingID' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'canceledYN' => $fake->randomDigitNotNull,
            'canceledByEmpSystemID' => $fake->randomDigitNotNull,
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
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceDirectFields);
    }
}
