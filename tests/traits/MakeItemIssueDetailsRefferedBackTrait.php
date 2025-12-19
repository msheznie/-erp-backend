<?php

use Faker\Factory as Faker;
use App\Models\ItemIssueDetailsRefferedBack;
use App\Repositories\ItemIssueDetailsRefferedBackRepository;

trait MakeItemIssueDetailsRefferedBackTrait
{
    /**
     * Create fake instance of ItemIssueDetailsRefferedBack and save it in database
     *
     * @param array $itemIssueDetailsRefferedBackFields
     * @return ItemIssueDetailsRefferedBack
     */
    public function makeItemIssueDetailsRefferedBack($itemIssueDetailsRefferedBackFields = [])
    {
        /** @var ItemIssueDetailsRefferedBackRepository $itemIssueDetailsRefferedBackRepo */
        $itemIssueDetailsRefferedBackRepo = App::make(ItemIssueDetailsRefferedBackRepository::class);
        $theme = $this->fakeItemIssueDetailsRefferedBackData($itemIssueDetailsRefferedBackFields);
        return $itemIssueDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of ItemIssueDetailsRefferedBack
     *
     * @param array $itemIssueDetailsRefferedBackFields
     * @return ItemIssueDetailsRefferedBack
     */
    public function fakeItemIssueDetailsRefferedBack($itemIssueDetailsRefferedBackFields = [])
    {
        return new ItemIssueDetailsRefferedBack($this->fakeItemIssueDetailsRefferedBackData($itemIssueDetailsRefferedBackFields));
    }

    /**
     * Get fake data of ItemIssueDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeItemIssueDetailsRefferedBackData($itemIssueDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'itemIssueDetailID' => $fake->randomDigitNotNull,
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
            'timesReferred' => $fake->randomDigitNotNull,
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
        ], $itemIssueDetailsRefferedBackFields);
    }
}
