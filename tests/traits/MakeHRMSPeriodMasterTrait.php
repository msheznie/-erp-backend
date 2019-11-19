<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\HRMSPeriodMaster;
use App\Repositories\HRMSPeriodMasterRepository;

trait MakeHRMSPeriodMasterTrait
{
    /**
     * Create fake instance of HRMSPeriodMaster and save it in database
     *
     * @param array $hRMSPeriodMasterFields
     * @return HRMSPeriodMaster
     */
    public function makeHRMSPeriodMaster($hRMSPeriodMasterFields = [])
    {
        /** @var HRMSPeriodMasterRepository $hRMSPeriodMasterRepo */
        $hRMSPeriodMasterRepo = \App::make(HRMSPeriodMasterRepository::class);
        $theme = $this->fakeHRMSPeriodMasterData($hRMSPeriodMasterFields);
        return $hRMSPeriodMasterRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSPeriodMaster
     *
     * @param array $hRMSPeriodMasterFields
     * @return HRMSPeriodMaster
     */
    public function fakeHRMSPeriodMaster($hRMSPeriodMasterFields = [])
    {
        return new HRMSPeriodMaster($this->fakeHRMSPeriodMasterData($hRMSPeriodMasterFields));
    }

    /**
     * Get fake data of HRMSPeriodMaster
     *
     * @param array $hRMSPeriodMasterFields
     * @return array
     */
    public function fakeHRMSPeriodMasterData($hRMSPeriodMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'periodMonth' => $fake->word,
            'periodYear' => $fake->randomDigitNotNull,
            'clientMonth' => $fake->word,
            'clientStartDate' => $fake->word,
            'clientEndDate' => $fake->word,
            'noOfDays' => $fake->randomDigitNotNull,
            'startDate' => $fake->date('Y-m-d H:i:s'),
            'endDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSPeriodMasterFields);
    }
}
