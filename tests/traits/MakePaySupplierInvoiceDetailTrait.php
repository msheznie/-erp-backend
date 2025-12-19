<?php

use Faker\Factory as Faker;
use App\Models\PaySupplierInvoiceDetail;
use App\Repositories\PaySupplierInvoiceDetailRepository;

trait MakePaySupplierInvoiceDetailTrait
{
    /**
     * Create fake instance of PaySupplierInvoiceDetail and save it in database
     *
     * @param array $paySupplierInvoiceDetailFields
     * @return PaySupplierInvoiceDetail
     */
    public function makePaySupplierInvoiceDetail($paySupplierInvoiceDetailFields = [])
    {
        /** @var PaySupplierInvoiceDetailRepository $paySupplierInvoiceDetailRepo */
        $paySupplierInvoiceDetailRepo = App::make(PaySupplierInvoiceDetailRepository::class);
        $theme = $this->fakePaySupplierInvoiceDetailData($paySupplierInvoiceDetailFields);
        return $paySupplierInvoiceDetailRepo->create($theme);
    }

    /**
     * Get fake instance of PaySupplierInvoiceDetail
     *
     * @param array $paySupplierInvoiceDetailFields
     * @return PaySupplierInvoiceDetail
     */
    public function fakePaySupplierInvoiceDetail($paySupplierInvoiceDetailFields = [])
    {
        return new PaySupplierInvoiceDetail($this->fakePaySupplierInvoiceDetailData($paySupplierInvoiceDetailFields));
    }

    /**
     * Get fake data of PaySupplierInvoiceDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaySupplierInvoiceDetailData($paySupplierInvoiceDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'apAutoID' => $fake->randomDigitNotNull,
            'matchingDocID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'addedDocumentSystemID' => $fake->randomDigitNotNull,
            'addedDocumentID' => $fake->word,
            'bookingInvSystemCode' => $fake->randomDigitNotNull,
            'bookingInvDocCode' => $fake->word,
            'bookingInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'addedDocumentType' => $fake->randomDigitNotNull,
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'supplierInvoiceNo' => $fake->word,
            'supplierInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransER' => $fake->randomDigitNotNull,
            'supplierInvoiceAmount' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyER' => $fake->randomDigitNotNull,
            'supplierDefaultAmount' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrencyID' => $fake->randomDigitNotNull,
            'comRptER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'supplierPaymentCurrencyID' => $fake->randomDigitNotNull,
            'supplierPaymentER' => $fake->randomDigitNotNull,
            'supplierPaymentAmount' => $fake->randomDigitNotNull,
            'paymentBalancedAmount' => $fake->randomDigitNotNull,
            'paymentSupplierDefaultAmount' => $fake->randomDigitNotNull,
            'paymentLocalAmount' => $fake->randomDigitNotNull,
            'paymentComRptAmount' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'modifiedUserID' => $fake->word,
            'modifiedPCID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $paySupplierInvoiceDetailFields);
    }
}
