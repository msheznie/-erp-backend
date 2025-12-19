<?php

use Faker\Factory as Faker;
use App\Models\StockReceive;
use App\Repositories\StockReceiveRepository;

trait MakeStockReceiveTrait
{
    /**
     * Create fake instance of StockReceive and save it in database
     *
     * @param array $stockReceiveFields
     * @return StockReceive
     */
    public function makeStockReceive($stockReceiveFields = [])
    {
        /** @var StockReceiveRepository $stockReceiveRepo */
        $stockReceiveRepo = App::make(StockReceiveRepository::class);
        $theme = $this->fakeStockReceiveData($stockReceiveFields);
        return $stockReceiveRepo->create($theme);
    }

    /**
     * Get fake instance of StockReceive
     *
     * @param array $stockReceiveFields
     * @return StockReceive
     */
    public function fakeStockReceive($stockReceiveFields = [])
    {
        return new StockReceive($this->fakeStockReceiveData($stockReceiveFields));
    }

    /**
     * Get fake data of StockReceive
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockReceiveData($stockReceiveFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'stockReceiveCode' => $fake->word,
            'refNo' => $fake->word,
            'receivedDate' => $fake->date('Y-m-d H:i:s'),
            'comment' => $fake->word,
            'companyFromSystemID' => $fake->randomDigitNotNull,
            'companyFrom' => $fake->word,
            'companyToSystemID' => $fake->randomDigitNotNull,
            'companyTo' => $fake->word,
            'locationTo' => $fake->randomDigitNotNull,
            'locationFrom' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'timesReferred' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockReceiveFields);
    }
}
