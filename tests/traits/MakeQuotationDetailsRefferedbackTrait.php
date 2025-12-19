<?php

use Faker\Factory as Faker;
use App\Models\QuotationDetailsRefferedback;
use App\Repositories\QuotationDetailsRefferedbackRepository;

trait MakeQuotationDetailsRefferedbackTrait
{
    /**
     * Create fake instance of QuotationDetailsRefferedback and save it in database
     *
     * @param array $quotationDetailsRefferedbackFields
     * @return QuotationDetailsRefferedback
     */
    public function makeQuotationDetailsRefferedback($quotationDetailsRefferedbackFields = [])
    {
        /** @var QuotationDetailsRefferedbackRepository $quotationDetailsRefferedbackRepo */
        $quotationDetailsRefferedbackRepo = App::make(QuotationDetailsRefferedbackRepository::class);
        $theme = $this->fakeQuotationDetailsRefferedbackData($quotationDetailsRefferedbackFields);
        return $quotationDetailsRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of QuotationDetailsRefferedback
     *
     * @param array $quotationDetailsRefferedbackFields
     * @return QuotationDetailsRefferedback
     */
    public function fakeQuotationDetailsRefferedback($quotationDetailsRefferedbackFields = [])
    {
        return new QuotationDetailsRefferedback($this->fakeQuotationDetailsRefferedbackData($quotationDetailsRefferedbackFields));
    }

    /**
     * Get fake data of QuotationDetailsRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeQuotationDetailsRefferedbackData($quotationDetailsRefferedbackFields = [])
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
        ], $quotationDetailsRefferedbackFields);
    }
}
