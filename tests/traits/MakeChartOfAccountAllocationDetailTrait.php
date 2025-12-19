<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ChartOfAccountAllocationDetail;
use App\Repositories\ChartOfAccountAllocationDetailRepository;

trait MakeChartOfAccountAllocationDetailTrait
{
    /**
     * Create fake instance of ChartOfAccountAllocationDetail and save it in database
     *
     * @param array $chartOfAccountAllocationDetailFields
     * @return ChartOfAccountAllocationDetail
     */
    public function makeChartOfAccountAllocationDetail($chartOfAccountAllocationDetailFields = [])
    {
        /** @var ChartOfAccountAllocationDetailRepository $chartOfAccountAllocationDetailRepo */
        $chartOfAccountAllocationDetailRepo = \App::make(ChartOfAccountAllocationDetailRepository::class);
        $theme = $this->fakeChartOfAccountAllocationDetailData($chartOfAccountAllocationDetailFields);
        return $chartOfAccountAllocationDetailRepo->create($theme);
    }

    /**
     * Get fake instance of ChartOfAccountAllocationDetail
     *
     * @param array $chartOfAccountAllocationDetailFields
     * @return ChartOfAccountAllocationDetail
     */
    public function fakeChartOfAccountAllocationDetail($chartOfAccountAllocationDetailFields = [])
    {
        return new ChartOfAccountAllocationDetail($this->fakeChartOfAccountAllocationDetailData($chartOfAccountAllocationDetailFields));
    }

    /**
     * Get fake data of ChartOfAccountAllocationDetail
     *
     * @param array $chartOfAccountAllocationDetailFields
     * @return array
     */
    public function fakeChartOfAccountAllocationDetailData($chartOfAccountAllocationDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'chartOfAccountAllocationMasterID' => $fake->randomDigitNotNull,
            'companyid' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'allocationmaid' => $fake->randomDigitNotNull,
            'productLineCode' => $fake->word,
            'productLineID' => $fake->randomDigitNotNull,
            'percentage' => $fake->randomDigitNotNull,
            'timestamp' => $fake->word
        ], $chartOfAccountAllocationDetailFields);
    }
}
