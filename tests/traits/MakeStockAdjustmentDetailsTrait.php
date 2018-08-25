<?php

use Faker\Factory as Faker;
use App\Models\StockAdjustmentDetails;
use App\Repositories\StockAdjustmentDetailsRepository;

trait MakeStockAdjustmentDetailsTrait
{
    /**
     * Create fake instance of StockAdjustmentDetails and save it in database
     *
     * @param array $stockAdjustmentDetailsFields
     * @return StockAdjustmentDetails
     */
    public function makeStockAdjustmentDetails($stockAdjustmentDetailsFields = [])
    {
        /** @var StockAdjustmentDetailsRepository $stockAdjustmentDetailsRepo */
        $stockAdjustmentDetailsRepo = App::make(StockAdjustmentDetailsRepository::class);
        $theme = $this->fakeStockAdjustmentDetailsData($stockAdjustmentDetailsFields);
        return $stockAdjustmentDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of StockAdjustmentDetails
     *
     * @param array $stockAdjustmentDetailsFields
     * @return StockAdjustmentDetails
     */
    public function fakeStockAdjustmentDetails($stockAdjustmentDetailsFields = [])
    {
        return new StockAdjustmentDetails($this->fakeStockAdjustmentDetailsData($stockAdjustmentDetailsFields));
    }

    /**
     * Get fake data of StockAdjustmentDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockAdjustmentDetailsData($stockAdjustmentDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockAdjustmentAutoID' => $fake->randomDigitNotNull,
            'stockAdjustmentAutoIDCode' => $fake->word,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'itemUnitOfMeasure' => $fake->randomDigitNotNull,
            'partNumber' => $fake->word,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'noQty' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'currentWacLocalCurrencyID' => $fake->randomDigitNotNull,
            'currentWaclocal' => $fake->randomDigitNotNull,
            'currentWacRptCurrencyID' => $fake->randomDigitNotNull,
            'currentWacRpt' => $fake->randomDigitNotNull,
            'wacAdjLocal' => $fake->randomDigitNotNull,
            'wacAdjRptER' => $fake->randomDigitNotNull,
            'wacAdjRpt' => $fake->randomDigitNotNull,
            'currenctStockQty' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockAdjustmentDetailsFields);
    }
}
