<?php

use Faker\Factory as Faker;
use App\Models\StockTransferDetailsRefferedBack;
use App\Repositories\StockTransferDetailsRefferedBackRepository;

trait MakeStockTransferDetailsRefferedBackTrait
{
    /**
     * Create fake instance of StockTransferDetailsRefferedBack and save it in database
     *
     * @param array $stockTransferDetailsRefferedBackFields
     * @return StockTransferDetailsRefferedBack
     */
    public function makeStockTransferDetailsRefferedBack($stockTransferDetailsRefferedBackFields = [])
    {
        /** @var StockTransferDetailsRefferedBackRepository $stockTransferDetailsRefferedBackRepo */
        $stockTransferDetailsRefferedBackRepo = App::make(StockTransferDetailsRefferedBackRepository::class);
        $theme = $this->fakeStockTransferDetailsRefferedBackData($stockTransferDetailsRefferedBackFields);
        return $stockTransferDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of StockTransferDetailsRefferedBack
     *
     * @param array $stockTransferDetailsRefferedBackFields
     * @return StockTransferDetailsRefferedBack
     */
    public function fakeStockTransferDetailsRefferedBack($stockTransferDetailsRefferedBackFields = [])
    {
        return new StockTransferDetailsRefferedBack($this->fakeStockTransferDetailsRefferedBackData($stockTransferDetailsRefferedBackFields));
    }

    /**
     * Get fake data of StockTransferDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockTransferDetailsRefferedBackData($stockTransferDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockTransferDetailsID' => $fake->randomDigitNotNull,
            'stockTransferAutoID' => $fake->randomDigitNotNull,
            'stockTransferCode' => $fake->word,
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
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
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockTransferDetailsRefferedBackFields);
    }
}
