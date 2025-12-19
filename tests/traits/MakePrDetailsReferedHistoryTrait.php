<?php

use Faker\Factory as Faker;
use App\Models\PrDetailsReferedHistory;
use App\Repositories\PrDetailsReferedHistoryRepository;

trait MakePrDetailsReferedHistoryTrait
{
    /**
     * Create fake instance of PrDetailsReferedHistory and save it in database
     *
     * @param array $prDetailsReferedHistoryFields
     * @return PrDetailsReferedHistory
     */
    public function makePrDetailsReferedHistory($prDetailsReferedHistoryFields = [])
    {
        /** @var PrDetailsReferedHistoryRepository $prDetailsReferedHistoryRepo */
        $prDetailsReferedHistoryRepo = App::make(PrDetailsReferedHistoryRepository::class);
        $theme = $this->fakePrDetailsReferedHistoryData($prDetailsReferedHistoryFields);
        return $prDetailsReferedHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of PrDetailsReferedHistory
     *
     * @param array $prDetailsReferedHistoryFields
     * @return PrDetailsReferedHistory
     */
    public function fakePrDetailsReferedHistory($prDetailsReferedHistoryFields = [])
    {
        return new PrDetailsReferedHistory($this->fakePrDetailsReferedHistoryData($prDetailsReferedHistoryFields));
    }

    /**
     * Get fake data of PrDetailsReferedHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakePrDetailsReferedHistoryData($prDetailsReferedHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'purchaseRequestID' => $fake->randomDigitNotNull,
            'itemCode' => $fake->word,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->word,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'quantityRequested' => $fake->randomDigitNotNull,
            'estimatedCost' => $fake->randomDigitNotNull,
            'quantityOnOrder' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'unitOfMeasure' => $fake->word,
            'quantityInHand' => $fake->randomDigitNotNull,
            'timesReffered' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'partNumber' => $fake->word
        ], $prDetailsReferedHistoryFields);
    }
}
