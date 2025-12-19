<?php

use Faker\Factory as Faker;
use App\Models\PeriodMaster;
use App\Repositories\PeriodMasterRepository;

trait MakePeriodMasterTrait
{
    /**
     * Create fake instance of PeriodMaster and save it in database
     *
     * @param array $periodMasterFields
     * @return PeriodMaster
     */
    public function makePeriodMaster($periodMasterFields = [])
    {
        /** @var PeriodMasterRepository $periodMasterRepo */
        $periodMasterRepo = App::make(PeriodMasterRepository::class);
        $theme = $this->fakePeriodMasterData($periodMasterFields);
        return $periodMasterRepo->create($theme);
    }

    /**
     * Get fake instance of PeriodMaster
     *
     * @param array $periodMasterFields
     * @return PeriodMaster
     */
    public function fakePeriodMaster($periodMasterFields = [])
    {
        return new PeriodMaster($this->fakePeriodMasterData($periodMasterFields));
    }

    /**
     * Get fake data of PeriodMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakePeriodMasterData($periodMasterFields = [])
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
        ], $periodMasterFields);
    }
}
