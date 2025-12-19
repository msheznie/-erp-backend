<?php

use Faker\Factory as Faker;
use App\Models\ItemReturnDetails;
use App\Repositories\ItemReturnDetailsRepository;

trait MakeItemReturnDetailsTrait
{
    /**
     * Create fake instance of ItemReturnDetails and save it in database
     *
     * @param array $itemReturnDetailsFields
     * @return ItemReturnDetails
     */
    public function makeItemReturnDetails($itemReturnDetailsFields = [])
    {
        /** @var ItemReturnDetailsRepository $itemReturnDetailsRepo */
        $itemReturnDetailsRepo = App::make(ItemReturnDetailsRepository::class);
        $theme = $this->fakeItemReturnDetailsData($itemReturnDetailsFields);
        return $itemReturnDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of ItemReturnDetails
     *
     * @param array $itemReturnDetailsFields
     * @return ItemReturnDetails
     */
    public function fakeItemReturnDetails($itemReturnDetailsFields = [])
    {
        return new ItemReturnDetails($this->fakeItemReturnDetailsData($itemReturnDetailsFields));
    }

    /**
     * Get fake data of ItemReturnDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemReturnDetailsData($itemReturnDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemReturnAutoID' => $fake->randomDigitNotNull,
            'itemReturnCode' => $fake->word,
            'issueCodeSystem' => $fake->randomDigitNotNull,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'unitOfMeasureIssued' => $fake->randomDigitNotNull,
            'qtyIssued' => $fake->randomDigitNotNull,
            'convertionMeasureVal' => $fake->randomDigitNotNull,
            'qtyIssuedDefaultMeasure' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'unitCostLocal' => $fake->randomDigitNotNull,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'unitCostRpt' => $fake->randomDigitNotNull,
            'qtyFromIssue' => $fake->randomDigitNotNull,
            'selectedForBillingOP' => $fake->randomDigitNotNull,
            'selectedForBillingOPtemp' => $fake->randomDigitNotNull,
            'opTicketNo' => $fake->randomDigitNotNull,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemReturnDetailsFields);
    }
}
