<?php

use Faker\Factory as Faker;
use App\Models\StockAdjustmentDetailsRefferedBack;
use App\Repositories\StockAdjustmentDetailsRefferedBackRepository;

trait MakeStockAdjustmentDetailsRefferedBackTrait
{
    /**
     * Create fake instance of StockAdjustmentDetailsRefferedBack and save it in database
     *
     * @param array $stockAdjustmentDetailsRefferedBackFields
     * @return StockAdjustmentDetailsRefferedBack
     */
    public function makeStockAdjustmentDetailsRefferedBack($stockAdjustmentDetailsRefferedBackFields = [])
    {
        /** @var StockAdjustmentDetailsRefferedBackRepository $stockAdjustmentDetailsRefferedBackRepo */
        $stockAdjustmentDetailsRefferedBackRepo = App::make(StockAdjustmentDetailsRefferedBackRepository::class);
        $theme = $this->fakeStockAdjustmentDetailsRefferedBackData($stockAdjustmentDetailsRefferedBackFields);
        return $stockAdjustmentDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of StockAdjustmentDetailsRefferedBack
     *
     * @param array $stockAdjustmentDetailsRefferedBackFields
     * @return StockAdjustmentDetailsRefferedBack
     */
    public function fakeStockAdjustmentDetailsRefferedBack($stockAdjustmentDetailsRefferedBackFields = [])
    {
        return new StockAdjustmentDetailsRefferedBack($this->fakeStockAdjustmentDetailsRefferedBackData($stockAdjustmentDetailsRefferedBackFields));
    }

    /**
     * Get fake data of StockAdjustmentDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockAdjustmentDetailsRefferedBackData($stockAdjustmentDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockAdjustmentDetailsAutoID' => $fake->randomDigitNotNull,
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
            'wacAdjLocalER' => $fake->randomDigitNotNull,
            'currenctStockQty' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockAdjustmentDetailsRefferedBackFields);
    }
}
