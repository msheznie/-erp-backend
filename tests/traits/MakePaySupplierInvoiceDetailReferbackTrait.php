<?php

use Faker\Factory as Faker;
use App\Models\PaySupplierInvoiceDetailReferback;
use App\Repositories\PaySupplierInvoiceDetailReferbackRepository;

trait MakePaySupplierInvoiceDetailReferbackTrait
{
    /**
     * Create fake instance of PaySupplierInvoiceDetailReferback and save it in database
     *
     * @param array $paySupplierInvoiceDetailReferbackFields
     * @return PaySupplierInvoiceDetailReferback
     */
    public function makePaySupplierInvoiceDetailReferback($paySupplierInvoiceDetailReferbackFields = [])
    {
        /** @var PaySupplierInvoiceDetailReferbackRepository $paySupplierInvoiceDetailReferbackRepo */
        $paySupplierInvoiceDetailReferbackRepo = App::make(PaySupplierInvoiceDetailReferbackRepository::class);
        $theme = $this->fakePaySupplierInvoiceDetailReferbackData($paySupplierInvoiceDetailReferbackFields);
        return $paySupplierInvoiceDetailReferbackRepo->create($theme);
    }

    /**
     * Get fake instance of PaySupplierInvoiceDetailReferback
     *
     * @param array $paySupplierInvoiceDetailReferbackFields
     * @return PaySupplierInvoiceDetailReferback
     */
    public function fakePaySupplierInvoiceDetailReferback($paySupplierInvoiceDetailReferbackFields = [])
    {
        return new PaySupplierInvoiceDetailReferback($this->fakePaySupplierInvoiceDetailReferbackData($paySupplierInvoiceDetailReferbackFields));
    }

    /**
     * Get fake data of PaySupplierInvoiceDetailReferback
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaySupplierInvoiceDetailReferbackData($paySupplierInvoiceDetailReferbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'payDetailAutoID' => $fake->randomDigitNotNull,
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
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $paySupplierInvoiceDetailReferbackFields);
    }
}
