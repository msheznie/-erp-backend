<?php

use Faker\Factory as Faker;
use App\Models\ItemIssueDetails;
use App\Repositories\ItemIssueDetailsRepository;

trait MakeItemIssueDetailsTrait
{
    /**
     * Create fake instance of ItemIssueDetails and save it in database
     *
     * @param array $itemIssueDetailsFields
     * @return ItemIssueDetails
     */
    public function makeItemIssueDetails($itemIssueDetailsFields = [])
    {
        /** @var ItemIssueDetailsRepository $itemIssueDetailsRepo */
        $itemIssueDetailsRepo = App::make(ItemIssueDetailsRepository::class);
        $theme = $this->fakeItemIssueDetailsData($itemIssueDetailsFields);
        return $itemIssueDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of ItemIssueDetails
     *
     * @param array $itemIssueDetailsFields
     * @return ItemIssueDetails
     */
    public function fakeItemIssueDetails($itemIssueDetailsFields = [])
    {
        return new ItemIssueDetails($this->fakeItemIssueDetailsData($itemIssueDetailsFields));
    }

    /**
     * Get fake data of ItemIssueDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemIssueDetailsData($itemIssueDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemIssueAutoID' => $fake->randomDigitNotNull,
            'itemIssueCode' => $fake->word,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'unitOfMeasureIssued' => $fake->randomDigitNotNull,
            'clientReferenceNumber' => $fake->word,
            'qtyRequested' => $fake->randomDigitNotNull,
            'qtyIssued' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'convertionMeasureVal' => $fake->randomDigitNotNull,
            'qtyIssuedDefaultMeasure' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'issueCostLocal' => $fake->randomDigitNotNull,
            'issueCostLocalTotal' => $fake->randomDigitNotNull,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'issueCostRpt' => $fake->randomDigitNotNull,
            'issueCostRptTotal' => $fake->randomDigitNotNull,
            'currentStockQty' => $fake->randomDigitNotNull,
            'currentWareHouseStockQty' => $fake->randomDigitNotNull,
            'currentStockQtyInDamageReturn' => $fake->randomDigitNotNull,
            'maxQty' => $fake->randomDigitNotNull,
            'minQty' => $fake->randomDigitNotNull,
            'selectedForBillingOP' => $fake->randomDigitNotNull,
            'selectedForBillingOPtemp' => $fake->randomDigitNotNull,
            'opTicketNo' => $fake->randomDigitNotNull,
            'del' => $fake->randomDigitNotNull,
            'backLoad' => $fake->randomDigitNotNull,
            'used' => $fake->randomDigitNotNull,
            'grvDocumentNO' => $fake->word,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'p1' => $fake->randomDigitNotNull,
            'p2' => $fake->randomDigitNotNull,
            'p3' => $fake->randomDigitNotNull,
            'p4' => $fake->randomDigitNotNull,
            'p5' => $fake->randomDigitNotNull,
            'p6' => $fake->randomDigitNotNull,
            'p7' => $fake->randomDigitNotNull,
            'p8' => $fake->randomDigitNotNull,
            'p9' => $fake->randomDigitNotNull,
            'p10' => $fake->randomDigitNotNull,
            'p11' => $fake->randomDigitNotNull,
            'p12' => $fake->randomDigitNotNull,
            'p13' => $fake->randomDigitNotNull,
            'pl10' => $fake->word,
            'pl3' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $itemIssueDetailsFields);
    }
}
