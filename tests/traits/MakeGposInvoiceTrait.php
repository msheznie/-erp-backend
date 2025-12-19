<?php

use Faker\Factory as Faker;
use App\Models\GposInvoice;
use App\Repositories\GposInvoiceRepository;

trait MakeGposInvoiceTrait
{
    /**
     * Create fake instance of GposInvoice and save it in database
     *
     * @param array $gposInvoiceFields
     * @return GposInvoice
     */
    public function makeGposInvoice($gposInvoiceFields = [])
    {
        /** @var GposInvoiceRepository $gposInvoiceRepo */
        $gposInvoiceRepo = App::make(GposInvoiceRepository::class);
        $theme = $this->fakeGposInvoiceData($gposInvoiceFields);
        return $gposInvoiceRepo->create($theme);
    }

    /**
     * Get fake instance of GposInvoice
     *
     * @param array $gposInvoiceFields
     * @return GposInvoice
     */
    public function fakeGposInvoice($gposInvoiceFields = [])
    {
        return new GposInvoice($this->fakeGposInvoiceData($gposInvoiceFields));
    }

    /**
     * Get fake data of GposInvoice
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGposInvoiceData($gposInvoiceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'segmentID' => $fake->randomDigitNotNull,
            'segmentCode' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'invoiceSequenceNo' => $fake->randomDigitNotNull,
            'invoiceCode' => $fake->word,
            'financialYearID' => $fake->randomDigitNotNull,
            'financialPeriodID' => $fake->randomDigitNotNull,
            'FYBegin' => $fake->word,
            'FYEnd' => $fake->word,
            'FYPeriodDateFrom' => $fake->word,
            'FYPeriodDateTo' => $fake->word,
            'customerID' => $fake->randomDigitNotNull,
            'customerCode' => $fake->word,
            'counterID' => $fake->randomDigitNotNull,
            'shiftID' => $fake->randomDigitNotNull,
            'memberID' => $fake->word,
            'memberName' => $fake->word,
            'memberContactNo' => $fake->word,
            'memberEmail' => $fake->word,
            'invoiceDate' => $fake->word,
            'subTotal' => $fake->randomDigitNotNull,
            'discountPercentage' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'netTotal' => $fake->randomDigitNotNull,
            'paidAmount' => $fake->randomDigitNotNull,
            'balanceAmount' => $fake->randomDigitNotNull,
            'cashAmount' => $fake->randomDigitNotNull,
            'chequeAmount' => $fake->randomDigitNotNull,
            'chequeNo' => $fake->word,
            'chequeDate' => $fake->word,
            'cardAmount' => $fake->randomDigitNotNull,
            'creditNoteID' => $fake->randomDigitNotNull,
            'creditNoteAmount' => $fake->randomDigitNotNull,
            'giftCardID' => $fake->randomDigitNotNull,
            'giftCardAmount' => $fake->randomDigitNotNull,
            'cardNumber' => $fake->randomDigitNotNull,
            'cardRefNo' => $fake->randomDigitNotNull,
            'cardBank' => $fake->randomDigitNotNull,
            'isCreditSales' => $fake->randomDigitNotNull,
            'creditSalesAmount' => $fake->randomDigitNotNull,
            'wareHouseAutoID' => $fake->randomDigitNotNull,
            'wareHouseCode' => $fake->word,
            'wareHouseLocation' => $fake->word,
            'wareHouseDescription' => $fake->word,
            'transactionCurrencyID' => $fake->randomDigitNotNull,
            'transactionCurrency' => $fake->word,
            'transactionExchangeRate' => $fake->randomDigitNotNull,
            'transactionCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyLocalCurrencyID' => $fake->randomDigitNotNull,
            'companyLocalCurrency' => $fake->word,
            'companyLocalExchangeRate' => $fake->randomDigitNotNull,
            'companyLocalCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingCurrency' => $fake->word,
            'companyReportingExchangeRate' => $fake->randomDigitNotNull,
            'companyReportingCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'customerCurrencyID' => $fake->randomDigitNotNull,
            'customerCurrency' => $fake->word,
            'customerCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'customerCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'customerReceivableAutoID' => $fake->randomDigitNotNull,
            'customerReceivableSystemGLCode' => $fake->word,
            'customerReceivableGLAccount' => $fake->word,
            'customerReceivableDescription' => $fake->word,
            'customerReceivableType' => $fake->word,
            'bankGLAutoID' => $fake->randomDigitNotNull,
            'bankSystemGLCode' => $fake->word,
            'bankGLAccount' => $fake->word,
            'bankGLDescription' => $fake->word,
            'bankGLType' => $fake->word,
            'bankCurrencyID' => $fake->randomDigitNotNull,
            'bankCurrency' => $fake->word,
            'bankCurrencyExchangeRate' => $fake->randomDigitNotNull,
            'bankCurrencyDecimalPlaces' => $fake->randomDigitNotNull,
            'bankCurrencyAmount' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdUserName' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedUserName' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $gposInvoiceFields);
    }
}
