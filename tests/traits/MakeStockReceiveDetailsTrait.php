<?php

use Faker\Factory as Faker;
use App\Models\StockReceiveDetails;
use App\Repositories\StockReceiveDetailsRepository;

trait MakeStockReceiveDetailsTrait
{
    /**
     * Create fake instance of StockReceiveDetails and save it in database
     *
     * @param array $stockReceiveDetailsFields
     * @return StockReceiveDetails
     */
    public function makeStockReceiveDetails($stockReceiveDetailsFields = [])
    {
        /** @var StockReceiveDetailsRepository $stockReceiveDetailsRepo */
        $stockReceiveDetailsRepo = App::make(StockReceiveDetailsRepository::class);
        $theme = $this->fakeStockReceiveDetailsData($stockReceiveDetailsFields);
        return $stockReceiveDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of StockReceiveDetails
     *
     * @param array $stockReceiveDetailsFields
     * @return StockReceiveDetails
     */
    public function fakeStockReceiveDetails($stockReceiveDetailsFields = [])
    {
        return new StockReceiveDetails($this->fakeStockReceiveDetailsData($stockReceiveDetailsFields));
    }

    /**
     * Get fake data of StockReceiveDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockReceiveDetailsData($stockReceiveDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'localCurrencyID' => $fake->randomDigitNotNull,
            'unitCostLocal' => $fake->randomDigitNotNull,
            'reportingCurrencyID' => $fake->randomDigitNotNull,
            'unitCostRpt' => $fake->randomDigitNotNull,
            'qty' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $stockReceiveDetailsFields);
    }
}
