<?php

use Faker\Factory as Faker;
use App\Models\CustomerInvoiceDirectDetRefferedback;
use App\Repositories\CustomerInvoiceDirectDetRefferedbackRepository;

trait MakeCustomerInvoiceDirectDetRefferedbackTrait
{
    /**
     * Create fake instance of CustomerInvoiceDirectDetRefferedback and save it in database
     *
     * @param array $customerInvoiceDirectDetRefferedbackFields
     * @return CustomerInvoiceDirectDetRefferedback
     */
    public function makeCustomerInvoiceDirectDetRefferedback($customerInvoiceDirectDetRefferedbackFields = [])
    {
        /** @var CustomerInvoiceDirectDetRefferedbackRepository $customerInvoiceDirectDetRefferedbackRepo */
        $customerInvoiceDirectDetRefferedbackRepo = App::make(CustomerInvoiceDirectDetRefferedbackRepository::class);
        $theme = $this->fakeCustomerInvoiceDirectDetRefferedbackData($customerInvoiceDirectDetRefferedbackFields);
        return $customerInvoiceDirectDetRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of CustomerInvoiceDirectDetRefferedback
     *
     * @param array $customerInvoiceDirectDetRefferedbackFields
     * @return CustomerInvoiceDirectDetRefferedback
     */
    public function fakeCustomerInvoiceDirectDetRefferedback($customerInvoiceDirectDetRefferedbackFields = [])
    {
        return new CustomerInvoiceDirectDetRefferedback($this->fakeCustomerInvoiceDirectDetRefferedbackData($customerInvoiceDirectDetRefferedbackFields));
    }

    /**
     * Get fake data of CustomerInvoiceDirectDetRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCustomerInvoiceDirectDetRefferedbackData($customerInvoiceDirectDetRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'custInvDirDetAutoID' => $fake->randomDigitNotNull,
            'custInvoiceDirectID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'glSystemID' => $fake->randomDigitNotNull,
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
            'discountLocalAmount' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'discountRptAmount' => $fake->randomDigitNotNull,
            'discountRate' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'performaMasterID' => $fake->randomDigitNotNull,
            'clientContractID' => $fake->word,
            'contractID' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $customerInvoiceDirectDetRefferedbackFields);
    }
}
