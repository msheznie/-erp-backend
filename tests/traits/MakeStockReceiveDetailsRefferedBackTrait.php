<?php

use Faker\Factory as Faker;
use App\Models\StockReceiveDetailsRefferedBack;
use App\Repositories\StockReceiveDetailsRefferedBackRepository;

trait MakeStockReceiveDetailsRefferedBackTrait
{
    /**
     * Create fake instance of StockReceiveDetailsRefferedBack and save it in database
     *
     * @param array $stockReceiveDetailsRefferedBackFields
     * @return StockReceiveDetailsRefferedBack
     */
    public function makeStockReceiveDetailsRefferedBack($stockReceiveDetailsRefferedBackFields = [])
    {
        /** @var StockReceiveDetailsRefferedBackRepository $stockReceiveDetailsRefferedBackRepo */
        $stockReceiveDetailsRefferedBackRepo = App::make(StockReceiveDetailsRefferedBackRepository::class);
        $theme = $this->fakeStockReceiveDetailsRefferedBackData($stockReceiveDetailsRefferedBackFields);
        return $stockReceiveDetailsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of StockReceiveDetailsRefferedBack
     *
     * @param array $stockReceiveDetailsRefferedBackFields
     * @return StockReceiveDetailsRefferedBack
     */
    public function fakeStockReceiveDetailsRefferedBack($stockReceiveDetailsRefferedBackFields = [])
    {
        return new StockReceiveDetailsRefferedBack($this->fakeStockReceiveDetailsRefferedBackData($stockReceiveDetailsRefferedBackFields));
    }

    /**
     * Get fake data of StockReceiveDetailsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockReceiveDetailsRefferedBackData($stockReceiveDetailsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockReceiveDetailsID' => $fake->randomDigitNotNull,
            'stockReceiveAutoID' => $fake->randomDigitNotNull,
            'stockReceiveCode' => $fake->word,
            'stockTransferAutoID' => $fake->randomDigitNotNull,
            'stockTransferCode' => $fake->word,
            'stockTransferDate' => $fake->date('Y-m-d H:i:s'),
            'itemCodeSystem' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'unitCostLocal' => $fake->randomDigitNotNull,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'unitCostRpt' => $fake->randomDigitNotNull,
            'qty' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $stockReceiveDetailsRefferedBackFields);
    }
}
