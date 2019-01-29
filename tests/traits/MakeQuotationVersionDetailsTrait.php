<?php

use Faker\Factory as Faker;
use App\Models\QuotationVersionDetails;
use App\Repositories\QuotationVersionDetailsRepository;

trait MakeQuotationVersionDetailsTrait
{
    /**
     * Create fake instance of QuotationVersionDetails and save it in database
     *
     * @param array $quotationVersionDetailsFields
     * @return QuotationVersionDetails
     */
    public function makeQuotationVersionDetails($quotationVersionDetailsFields = [])
    {
        /** @var QuotationVersionDetailsRepository $quotationVersionDetailsRepo */
        $quotationVersionDetailsRepo = App::make(QuotationVersionDetailsRepository::class);
        $theme = $this->fakeQuotationVersionDetailsData($quotationVersionDetailsFields);
        return $quotationVersionDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of QuotationVersionDetails
     *
     * @param array $quotationVersionDetailsFields
     * @return QuotationVersionDetails
     */
    public function fakeQuotationVersionDetails($quotationVersionDetailsFields = [])
    {
        return new QuotationVersionDetails($this->fakeQuotationVersionDetailsData($quotationVersionDetailsFields));
    }

    /**
     * Get fake data of QuotationVersionDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeQuotationVersionDetailsData($quotationVersionDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'quotationDetailsID' => $fake->randomDigitNotNull,
            'quotationMasterID' => $fake->randomDigitNotNull,
            'versionNo' => $fake->randomDigitNotNull,
            'itemAutoID' => $fake->randomDigitNotNull,
            'itemSystemCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemCategory' => $fake->word,
            'defaultUOMID' => $fake->randomDigitNotNull,
            'itemReferenceNo' => $fake->word,
            'defaultUOM' => $fake->word,
            'unitOfMeasureID' => $fake->randomDigitNotNull,
            'unitOfMeasure' => $fake->word,
            'conversionRateUOM' => $fake->randomDigitNotNull,
            'requestedQty' => $fake->randomDigitNotNull,
            'invoicedYN' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'remarks' => $fake->text,
            'unittransactionAmount' => $fake->randomDigitNotNull,
            'discountPercentage' => $fake->randomDigitNotNull,
            'discountAmount' => $fake->randomDigitNotNull,
            'discountTotal' => $fake->randomDigitNotNull,
            'transactionAmount' => $fake->randomDigitNotNull,
            'companyLocalAmount' => $fake->randomDigitNotNull,
            'companyReportingAmount' => $fake->randomDigitNotNull,
            'customerAmount' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'createdUserGroup' => $fake->randomDigitNotNull,
            'createdPCID' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserName' => $fake->word,
            'modifiedPCID' => $fake->word,
            'modifiedUserID' => $fake->word,
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserName' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $quotationVersionDetailsFields);
    }
}
