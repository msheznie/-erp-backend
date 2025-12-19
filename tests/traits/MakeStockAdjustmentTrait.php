<?php

use Faker\Factory as Faker;
use App\Models\StockAdjustment;
use App\Repositories\StockAdjustmentRepository;

trait MakeStockAdjustmentTrait
{
    /**
     * Create fake instance of StockAdjustment and save it in database
     *
     * @param array $stockAdjustmentFields
     * @return StockAdjustment
     */
    public function makeStockAdjustment($stockAdjustmentFields = [])
    {
        /** @var StockAdjustmentRepository $stockAdjustmentRepo */
        $stockAdjustmentRepo = App::make(StockAdjustmentRepository::class);
        $theme = $this->fakeStockAdjustmentData($stockAdjustmentFields);
        return $stockAdjustmentRepo->create($theme);
    }

    /**
     * Get fake instance of StockAdjustment
     *
     * @param array $stockAdjustmentFields
     * @return StockAdjustment
     */
    public function fakeStockAdjustment($stockAdjustmentFields = [])
    {
        return new StockAdjustment($this->fakeStockAdjustmentData($stockAdjustmentFields));
    }

    /**
     * Get fake data of StockAdjustment
     *
     * @param array $postFields
     * @return array
     */
    public function fakeStockAdjustmentData($stockAdjustmentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $stockAdjustmentFields);
    }
}
