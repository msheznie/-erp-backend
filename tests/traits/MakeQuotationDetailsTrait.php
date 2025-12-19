<?php

use Faker\Factory as Faker;
use App\Models\QuotationDetails;
use App\Repositories\QuotationDetailsRepository;

trait MakeQuotationDetailsTrait
{
    /**
     * Create fake instance of QuotationDetails and save it in database
     *
     * @param array $quotationDetailsFields
     * @return QuotationDetails
     */
    public function makeQuotationDetails($quotationDetailsFields = [])
    {
        /** @var QuotationDetailsRepository $quotationDetailsRepo */
        $quotationDetailsRepo = App::make(QuotationDetailsRepository::class);
        $theme = $this->fakeQuotationDetailsData($quotationDetailsFields);
        return $quotationDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of QuotationDetails
     *
     * @param array $quotationDetailsFields
     * @return QuotationDetails
     */
    public function fakeQuotationDetails($quotationDetailsFields = [])
    {
        return new QuotationDetails($this->fakeQuotationDetailsData($quotationDetailsFields));
    }

    /**
     * Get fake data of QuotationDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeQuotationDetailsData($quotationDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'quotationMasterID' => $fake->randomDigitNotNull,
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
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $quotationDetailsFields);
    }
}
