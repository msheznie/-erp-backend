<?php

use Faker\Factory as Faker;
use App\Models\StockTransferRefferedBack;
use App\Repositories\StockTransferRefferedBackRepository;

trait MakeStockTransferRefferedBackTrait
{
    /**
     * Create fake instance of StockTransferRefferedBack and save it in database
     *
     * @param array $stockTransferRefferedBackFields
     * @return StockTransferRefferedBack
     */
    public function makeStockTransferRefferedBack($stockTransferRefferedBackFields = [])
    {
        /** @var StockTransferRefferedBackRepository $stockTransferRefferedBackRepo */
        $stockTransferRefferedBackRepo = App::make(StockTransferRefferedBackRepository::class);
        $theme = $this->fakeStockTransferRefferedBackData($stockTransferRefferedBackFields);
        return $stockTransferRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of StockTransferRefferedBack
     *
     * @param array $stockTransferRefferedBackFields
     * @return StockTransferRefferedBack
     */
    public function fakeStockTransferRefferedBack($stockTransferRefferedBackFields = [])
    {
        return new StockTransferRefferedBack($this->fakeStockTransferRefferedBackData($stockTransferRefferedBackFields));
    }

    /**
     * Get fake data of StockTransferRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockTransferRefferedBackData($stockTransferRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockTransferAutoID' => $fake->randomDigitNotNull,
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
            'stockTransferCode' => $fake->word,
            'refNo' => $fake->word,
            'tranferDate' => $fake->date('Y-m-d H:i:s'),
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
            'fullyReceived' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'interCompanyTransferYN' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockTransferRefferedBackFields);
    }
}
