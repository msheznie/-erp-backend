<?php

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceDirectDetail;
use App\Repositories\CustomerInvoiceDirectDetailRepository;

trait MakeCustomerInvoiceDirectDetailTrait
{
    /**
     * Create fake instance of CustomerInvoiceDirectDetail and save it in database
     *
     * @param array $customerInvoiceDirectDetailFields
     * @return CustomerInvoiceDirectDetail
     */
    public function makeCustomerInvoiceDirectDetail($customerInvoiceDirectDetailFields = [])
    {
        /** @var CustomerInvoiceDirectDetailRepository $customerInvoiceDirectDetailRepo */
        $customerInvoiceDirectDetailRepo = App::make(CustomerInvoiceDirectDetailRepository::class);
        $theme = $this->fakeCustomerInvoiceDirectDetailData($customerInvoiceDirectDetailFields);
        return $customerInvoiceDirectDetailRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceDirectDetail
     *
     * @param array $customerInvoiceDirectDetailFields
     * @return CustomerInvoiceDirectDetail
     */
    public function fakeCustomerInvoiceDirectDetail($customerInvoiceDirectDetailFields = [])
    {
        return new CustomerInvoiceDirectDetail($this->fakeCustomerInvoiceDirectDetailData($customerInvoiceDirectDetailFields));
    }

    /**
     * Get fake data of CustomerInvoiceDirectDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerInvoiceDirectDetailData($customerInvoiceDirectDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custInvoiceDirectID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'accountType' => $fake->word,
            'comments' => $fake->text,
            'invoiceAmountCurrency' => $fake->randomDigitNotNull,
            'invoiceAmountCurrencyER' => $fake->randomDigitNotNull,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'invoiceQty' => $fake->randomDigitNotNull,
            'unitCost' => $fake->randomDigitNotNull,
            'invoiceAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'discountLocalAmount' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'discountRptAmount' => $fake->randomDigitNotNull,
            'discountRate' => $fake->randomDigitNotNull,
            'performaMasterID' => $fake->randomDigitNotNull,
            'clientContractID' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceDirectDetailFields);
    }
}
