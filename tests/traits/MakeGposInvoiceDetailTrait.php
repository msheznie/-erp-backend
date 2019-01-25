<?php

use Faker\Factory as Faker;
use App\Models\GposInvoiceDetail;
use App\Repositories\GposInvoiceDetailRepository;

trait MakeGposInvoiceDetailTrait
{
    /**
     * Create fake instance of GposInvoiceDetail and save it in database
     *
     * @param array $gposInvoiceDetailFields
     * @return GposInvoiceDetail
     */
    public function makeGposInvoiceDetail($gposInvoiceDetailFields = [])
    {
        /** @var GposInvoiceDetailRepository $gposInvoiceDetailRepo */
        $gposInvoiceDetailRepo = App::make(GposInvoiceDetailRepository::class);
        $theme = $this->fakeGposInvoiceDetailData($gposInvoiceDetailFields);
        return $gposInvoiceDetailRepo->create($theme);
    }

    /**
     * Get fake instance of GposInvoiceDetail
     *
     * @param array $gposInvoiceDetailFields
     * @return GposInvoiceDetail
     */
    public function fakeGposInvoiceDetail($gposInvoiceDetailFields = [])
    {
        return new GposInvoiceDetail($this->fakeGposInvoiceDetailData($gposInvoiceDetailFields));
    }

    /**
     * Get fake data of GposInvoiceDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeGposInvoiceDetailData($gposInvoiceDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'invoiceID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'itemAutoID' => $fake->randomDigitNotNull,
            'itemSystemCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemCategory' => $fake->word,
            'financeCategory' => $fake->randomDigitNotNull,
            'itemFinanceCategory' => $fake->randomDigitNotNull,
            'itemFinanceCategorySub' => $fake->randomDigitNotNull,
            'defaultUOM' => $fake->word,
            'unitOfMeasure' => $fake->word,
            'conversionRateUOM' => $fake->randomDigitNotNull,
            'expenseGLAutoID' => $fake->randomDigitNotNull,
            'expenseGLCode' => $fake->word,
            'expenseSystemGLCode' => $fake->word,
            'expenseGLDescription' => $fake->word,
            'expenseGLType' => $fake->word,
            'revenueGLAutoID' => $fake->randomDigitNotNull,
            'revenueGLCode' => $fake->word,
            'revenueSystemGLCode' => $fake->word,
            'revenueGLDescription' => $fake->word,
            'revenueGLType' => $fake->word,
            'assetGLAutoID' => $fake->randomDigitNotNull,
            'assetGLCode' => $fake->word,
            'assetSystemGLCode' => $fake->word,
            'assetGLDescription' => $fake->word,
            'assetGLType' => $fake->word,
            'qty' => $fake->randomDigitNotNull,
            'price' => $fake->randomDigitNotNull,
            'totalAmount' => $fake->randomDigitNotNull,
            'discountPercentage' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'wacAmount' => $fake->randomDigitNotNull,
            'netAmount' => $fake->randomDigitNotNull,
            'transactionCurrencyID' => $fake->randomDigitNotNull,
            'transactionCurrency' => $fake->word,
            'transactionAmountBeforeDiscount' => $fake->randomDigitNotNull,
            'transactionAmount' => $fake->randomDigitNotNull,
            'transactionCurrencyDecimalPlaces' => $fake->word,
            'transactionExchangeRate' => $fake->randomDigitNotNull,
            'companyLocalCurrencyID' => $fake->randomDigitNotNull,
            'companyLocalCurrency' => $fake->word,
            'companyLocalAmount' => $fake->randomDigitNotNull,
            'companyLocalExchangeRate' => $fake->randomDigitNotNull,
            'companyLocalCurrencyDecimalPlaces' => $fake->word,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingCurrency' => $fake->word,
            'companyReportingAmount' => $fake->randomDigitNotNull,
            'companyReportingCurrencyDecimalPlaces' => $fake->word,
            'companyReportingExchangeRate' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $gposInvoiceDetailFields);
    }
}
