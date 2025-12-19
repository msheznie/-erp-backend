<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\DashboardWidgetMaster;
use App\Repositories\DashboardWidgetMasterRepository;

trait MakeDashboardWidgetMasterTrait
{
    /**
     * Create fake instance of DashboardWidgetMaster and save it in database
     *
     * @param array $dashboardWidgetMasterFields
     * @return DashboardWidgetMaster
     */
    public function makeDashboardWidgetMaster($dashboardWidgetMasterFields = [])
    {
        /** @var DashboardWidgetMasterRepository $dashboardWidgetMasterRepo */
        $dashboardWidgetMasterRepo = \App::make(DashboardWidgetMasterRepository::class);
        $theme = $this->fakeDashboardWidgetMasterData($dashboardWidgetMasterFields);
        return $dashboardWidgetMasterRepo->create($theme);
    }

    /**
     * Get fake instance of DashboardWidgetMaster
     *
     * @param array $dashboardWidgetMasterFields
     * @return DashboardWidgetMaster
     */
    public function fakeDashboardWidgetMaster($dashboardWidgetMasterFields = [])
    {
        return new DashboardWidgetMaster($this->fakeDashboardWidgetMasterData($dashboardWidgetMasterFields));
    }

    /**
     * Get fake data of DashboardWidgetMaster
     *
     * @param array $dashboardWidgetMasterFields
     * @return array
     */
    public function fakeDashboardWidgetMasterData($dashboardWidgetMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'WidgetMasterName' => $fake->word,
            'departmentID' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->word,
            'widgetMasterIcon' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $dashboardWidgetMasterFields);
    }
}
