<?php

use Faker\Factory as Faker;
use App\Models\StockAdjustmentRefferedBack;
use App\Repositories\StockAdjustmentRefferedBackRepository;

trait MakeStockAdjustmentRefferedBackTrait
{
    /**
     * Create fake instance of StockAdjustmentRefferedBack and save it in database
     *
     * @param array $stockAdjustmentRefferedBackFields
     * @return StockAdjustmentRefferedBack
     */
    public function makeStockAdjustmentRefferedBack($stockAdjustmentRefferedBackFields = [])
    {
        /** @var StockAdjustmentRefferedBackRepository $stockAdjustmentRefferedBackRepo */
        $stockAdjustmentRefferedBackRepo = App::make(StockAdjustmentRefferedBackRepository::class);
        $theme = $this->fakeStockAdjustmentRefferedBackData($stockAdjustmentRefferedBackFields);
        return $stockAdjustmentRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of StockAdjustmentRefferedBack
     *
     * @param array $stockAdjustmentRefferedBackFields
     * @return StockAdjustmentRefferedBack
     */
    public function fakeStockAdjustmentRefferedBack($stockAdjustmentRefferedBackFields = [])
    {
        return new StockAdjustmentRefferedBack($this->fakeStockAdjustmentRefferedBackData($stockAdjustmentRefferedBackFields));
    }

    /**
     * Get fake data of StockAdjustmentRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockAdjustmentRefferedBackData($stockAdjustmentRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'stockAdjustmentAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'serialNo' => $fake->randomDigitNotNull,
            'stockAdjustmentCode' => $fake->word,
            'refNo' => $fake->word,
            'stockAdjustmentDate' => $fake->date('Y-m-d H:i:s'),
            'location' => $fake->randomDigitNotNull,
            'comment' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull
        ], $stockAdjustmentRefferedBackFields);
    }
}
