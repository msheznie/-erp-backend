<?php

use Faker\Factory as Faker;
use App\Models\DirectReceiptDetailsRefferedHistory;
use App\Repositories\DirectReceiptDetailsRefferedHistoryRepository;

trait MakeDirectReceiptDetailsRefferedHistoryTrait
{
    /**
     * Create fake instance of DirectReceiptDetailsRefferedHistory and save it in database
     *
     * @param array $directReceiptDetailsRefferedHistoryFields
     * @return DirectReceiptDetailsRefferedHistory
     */
    public function makeDirectReceiptDetailsRefferedHistory($directReceiptDetailsRefferedHistoryFields = [])
    {
        /** @var DirectReceiptDetailsRefferedHistoryRepository $directReceiptDetailsRefferedHistoryRepo */
        $directReceiptDetailsRefferedHistoryRepo = App::make(DirectReceiptDetailsRefferedHistoryRepository::class);
        $theme = $this->fakeDirectReceiptDetailsRefferedHistoryData($directReceiptDetailsRefferedHistoryFields);
        return $directReceiptDetailsRefferedHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of DirectReceiptDetailsRefferedHistory
     *
     * @param array $directReceiptDetailsRefferedHistoryFields
     * @return DirectReceiptDetailsRefferedHistory
     */
    public function fakeDirectReceiptDetailsRefferedHistory($directReceiptDetailsRefferedHistoryFields = [])
    {
        return new DirectReceiptDetailsRefferedHistory($this->fakeDirectReceiptDetailsRefferedHistoryData($directReceiptDetailsRefferedHistoryFields));
    }

    /**
     * Get fake data of DirectReceiptDetailsRefferedHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDirectReceiptDetailsRefferedHistoryData($directReceiptDetailsRefferedHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'directReceiptDetailsID' => $fake->randomDigitNotNull,
            'directReceiptAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'glSystemID' => $fake->randomDigitNotNull,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'contractID' => $fake->word,
            'contractUID' => $fake->randomDigitNotNull,
            'comments' => $fake->word,
            'DRAmountCurrency' => $fake->randomDigitNotNull,
            'DDRAmountCurrencyER' => $fake->randomDigitNotNull,
            'DRAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $directReceiptDetailsRefferedHistoryFields);
    }
}
