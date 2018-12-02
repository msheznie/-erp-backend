<?php

use Faker\Factory as Faker;
use App\Models\StockReceiveRefferedBack;
use App\Repositories\StockReceiveRefferedBackRepository;

trait MakeStockReceiveRefferedBackTrait
{
    /**
     * Create fake instance of StockReceiveRefferedBack and save it in database
     *
     * @param array $stockReceiveRefferedBackFields
     * @return StockReceiveRefferedBack
     */
    public function makeStockReceiveRefferedBack($stockReceiveRefferedBackFields = [])
    {
        /** @var StockReceiveRefferedBackRepository $stockReceiveRefferedBackRepo */
        $stockReceiveRefferedBackRepo = App::make(StockReceiveRefferedBackRepository::class);
        $theme = $this->fakeStockReceiveRefferedBackData($stockReceiveRefferedBackFields);
        return $stockReceiveRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of StockReceiveRefferedBack
     *
     * @param array $stockReceiveRefferedBackFields
     * @return StockReceiveRefferedBack
     */
    public function fakeStockReceiveRefferedBack($stockReceiveRefferedBackFields = [])
    {
        return new StockReceiveRefferedBack($this->fakeStockReceiveRefferedBackData($stockReceiveRefferedBackFields));
    }

    /**
     * Get fake data of StockReceiveRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockReceiveRefferedBackData($stockReceiveRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockReceiveAutoID' => $fake->randomDigitNotNull,
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
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
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
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'refferedBackYN' => $fake->randomDigitNotNull
        ], $stockReceiveRefferedBackFields);
    }
}
