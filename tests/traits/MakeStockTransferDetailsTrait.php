<?php

use Faker\Factory as Faker;
use App\Models\StockTransferDetails;
use App\Repositories\StockTransferDetailsRepository;

trait MakeStockTransferDetailsTrait
{
    /**
     * Create fake instance of StockTransferDetails and save it in database
     *
     * @param array $stockTransferDetailsFields
     * @return StockTransferDetails
     */
    public function makeStockTransferDetails($stockTransferDetailsFields = [])
    {
        /** @var StockTransferDetailsRepository $stockTransferDetailsRepo */
        $stockTransferDetailsRepo = App::make(StockTransferDetailsRepository::class);
        $theme = $this->fakeStockTransferDetailsData($stockTransferDetailsFields);
        return $stockTransferDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of StockTransferDetails
     *
     * @param array $stockTransferDetailsFields
     * @return StockTransferDetails
     */
    public function fakeStockTransferDetails($stockTransferDetailsFields = [])
    {
        return new StockTransferDetails($this->fakeStockTransferDetailsData($stockTransferDetailsFields));
    }

    /**
     * Get fake data of StockTransferDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockTransferDetailsData($stockTransferDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockTransferAutoID' => $fake->randomDigitNotNull,
            'stockTransferCode' => $fake->word,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'qty' => $fake->randomDigitNotNull,
            'currentStockQty' => $fake->randomDigitNotNull,
            'warehouseStockQty' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'unitCostLocal' => $fake->randomDigitNotNull,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'unitCostRpt' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'addedToRecieved' => $fake->randomDigitNotNull,
            'stockRecieved' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockTransferDetailsFields);
    }
}
