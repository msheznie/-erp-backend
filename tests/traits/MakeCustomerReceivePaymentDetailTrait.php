<?php

use Faker\Factory as Faker;
use App\Models\CustomerReceivePaymentDetail;
use App\Repositories\CustomerReceivePaymentDetailRepository;

trait MakeCustomerReceivePaymentDetailTrait
{
    /**
     * Create fake instance of CustomerReceivePaymentDetail and save it in database
     *
     * @param array $customerReceivePaymentDetailFields
     * @return CustomerReceivePaymentDetail
     */
    public function makeCustomerReceivePaymentDetail($customerReceivePaymentDetailFields = [])
    {
        /** @var CustomerReceivePaymentDetailRepository $customerReceivePaymentDetailRepo */
        $customerReceivePaymentDetailRepo = App::make(CustomerReceivePaymentDetailRepository::class);
        $theme = $this->fakeCustomerReceivePaymentDetailData($customerReceivePaymentDetailFields);
        return $customerReceivePaymentDetailRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerReceivePaymentDetail
     *
     * @param array $customerReceivePaymentDetailFields
     * @return CustomerReceivePaymentDetail
     */
    public function fakeCustomerReceivePaymentDetail($customerReceivePaymentDetailFields = [])
    {
        return new CustomerReceivePaymentDetail($this->fakeCustomerReceivePaymentDetailData($customerReceivePaymentDetailFields));
    }

    /**
     * Get fake data of CustomerReceivePaymentDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerReceivePaymentDetailData($customerReceivePaymentDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custReceivePaymentAutoID' => $fake->randomDigitNotNull,
            'arAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'matchingDocID' => $fake->randomDigitNotNull,
            'addedDocumentSystemID' => $fake->randomDigitNotNull,
            'addedDocumentID' => $fake->word,
            'bookingInvCodeSystem' => $fake->randomDigitNotNull,
            'bookingInvCode' => $fake->word,
            'bookingDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->word,
            'custTransactionCurrencyID' => $fake->randomDigitNotNull,
            'custTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'bookingAmountTrans' => $fake->randomDigitNotNull,
            'bookingAmountLocal' => $fake->randomDigitNotNull,
            'bookingAmountRpt' => $fake->randomDigitNotNull,
            'custReceiveCurrencyID' => $fake->randomDigitNotNull,
            'custReceiveCurrencyER' => $fake->randomDigitNotNull,
            'custbalanceAmount' => $fake->randomDigitNotNull,
            'receiveAmountTrans' => $fake->randomDigitNotNull,
            'receiveAmountLocal' => $fake->randomDigitNotNull,
            'receiveAmountRpt' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $customerReceivePaymentDetailFields);
    }
}
