<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ChartOfAccountAllocationDetailHistory;
use App\Repositories\ChartOfAccountAllocationDetailHistoryRepository;

trait MakeChartOfAccountAllocationDetailHistoryTrait
{
    /**
     * Create fake instance of ChartOfAccountAllocationDetailHistory and save it in database
     *
     * @param array $chartOfAccountAllocationDetailHistoryFields
     * @return ChartOfAccountAllocationDetailHistory
     */
    public function makeChartOfAccountAllocationDetailHistory($chartOfAccountAllocationDetailHistoryFields = [])
    {
        /** @var ChartOfAccountAllocationDetailHistoryRepository $chartOfAccountAllocationDetailHistoryRepo */
        $chartOfAccountAllocationDetailHistoryRepo = \App::make(ChartOfAccountAllocationDetailHistoryRepository::class);
        $theme = $this->fakeChartOfAccountAllocationDetailHistoryData($chartOfAccountAllocationDetailHistoryFields);
        return $chartOfAccountAllocationDetailHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of ChartOfAccountAllocationDetailHistory
     *
     * @param array $chartOfAccountAllocationDetailHistoryFields
     * @return ChartOfAccountAllocationDetailHistory
     */
    public function fakeChartOfAccountAllocationDetailHistory($chartOfAccountAllocationDetailHistoryFields = [])
    {
        return new ChartOfAccountAllocationDetailHistory($this->fakeChartOfAccountAllocationDetailHistoryData($chartOfAccountAllocationDetailHistoryFields));
    }

    /**
     * Get fake data of ChartOfAccountAllocationDetailHistory
     *
     * @param array $chartOfAccountAllocationDetailHistoryFields
     * @return array
     */
    public function fakeChartOfAccountAllocationDetailHistoryData($chartOfAccountAllocationDetailHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'jvMasterAutoId' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'percentage' => $fake->randomDigitNotNull,
            'productLineID' => $fake->randomDigitNotNull,
            'productLineCode' => $fake->word,
            'allocationmaid' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyid' => $fake->word,
            'chartOfAccountAllocationMasterID' => $fake->randomDigitNotNull
        ], $chartOfAccountAllocationDetailHistoryFields);
    }
}
