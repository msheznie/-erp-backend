<?php

use Faker\Factory as Faker;
use App\Models\ItemReturnDetailsRefferedBack;
use App\Repositories\ItemReturnDetailsRefferedBackRepository;

trait MakeItemReturnDetailsRefferedBackTrait
{
    /**
     * Create fake instance of ItemReturnDetailsRefferedBack and save it in database
     *
     * @param array $itemReturnDetailsRefferedBackFields
     * @return ItemReturnDetailsRefferedBack
     */
    public function makeItemReturnDetailsRefferedBack($itemReturnDetailsRefferedBackFields = [])
    {
        /** @var ItemReturnDetailsRefferedBackRepository $itemReturnDetailsRefferedBackRepo */
        $itemReturnDetailsRefferedBackRepo = App::make(ItemReturnDetailsRefferedBackRepository::class);
        $theme = $this->fakeItemReturnDetailsRefferedBackData($itemReturnDetailsRefferedBackFields);
        return $itemReturnDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of ItemReturnDetailsRefferedBack
     *
     * @param array $itemReturnDetailsRefferedBackFields
     * @return ItemReturnDetailsRefferedBack
     */
    public function fakeItemReturnDetailsRefferedBack($itemReturnDetailsRefferedBackFields = [])
    {
        return new ItemReturnDetailsRefferedBack($this->fakeItemReturnDetailsRefferedBackData($itemReturnDetailsRefferedBackFields));
    }

    /**
     * Get fake data of ItemReturnDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemReturnDetailsRefferedBackData($itemReturnDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemReturnDetailID' => $fake->randomDigitNotNull,
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
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemReturnDetailsRefferedBackFields);
    }
}
