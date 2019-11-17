<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ChartOfAccountAllocationMaster;
use App\Repositories\ChartOfAccountAllocationMasterRepository;

trait MakeChartOfAccountAllocationMasterTrait
{
    /**
     * Create fake instance of ChartOfAccountAllocationMaster and save it in database
     *
     * @param array $chartOfAccountAllocationMasterFields
     * @return ChartOfAccountAllocationMaster
     */
    public function makeChartOfAccountAllocationMaster($chartOfAccountAllocationMasterFields = [])
    {
        /** @var ChartOfAccountAllocationMasterRepository $chartOfAccountAllocationMasterRepo */
        $chartOfAccountAllocationMasterRepo = \App::make(ChartOfAccountAllocationMasterRepository::class);
        $theme = $this->fakeChartOfAccountAllocationMasterData($chartOfAccountAllocationMasterFields);
        return $chartOfAccountAllocationMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ChartOfAccountAllocationMaster
     *
     * @param array $chartOfAccountAllocationMasterFields
     * @return ChartOfAccountAllocationMaster
     */
    public function fakeChartOfAccountAllocationMaster($chartOfAccountAllocationMasterFields = [])
    {
        return new ChartOfAccountAllocationMaster($this->fakeChartOfAccountAllocationMasterData($chartOfAccountAllocationMasterFields));
    }

    /**
     * Get fake data of ChartOfAccountAllocationMaster
     *
     * @param array $chartOfAccountAllocationMasterFields
     * @return array
     */
    public function fakeChartOfAccountAllocationMasterData($chartOfAccountAllocationMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'allocationmaid' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'chartOfAccountCode' => $fake->word,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'timestamp' => $fake->word
        ], $chartOfAccountAllocationMasterFields);
    }
}
