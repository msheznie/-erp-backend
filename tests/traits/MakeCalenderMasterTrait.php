<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CalenderMaster;
use App\Repositories\CalenderMasterRepository;

trait MakeCalenderMasterTrait
{
    /**
     * Create fake instance of CalenderMaster and save it in database
     *
     * @param array $calenderMasterFields
     * @return CalenderMaster
     */
    public function makeCalenderMaster($calenderMasterFields = [])
    {
        /** @var CalenderMasterRepository $calenderMasterRepo */
        $calenderMasterRepo = \App::make(CalenderMasterRepository::class);
        $theme = $this->fakeCalenderMasterData($calenderMasterFields);
        return $calenderMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CalenderMaster
     *
     * @param array $calenderMasterFields
     * @return CalenderMaster
     */
    public function fakeCalenderMaster($calenderMasterFields = [])
    {
        return new CalenderMaster($this->fakeCalenderMasterData($calenderMasterFields));
    }

    /**
     * Get fake data of CalenderMaster
     *
     * @param array $calenderMasterFields
     * @return array
     */
    public function fakeCalenderMasterData($calenderMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'calDate' => $fake->date('Y-m-d H:i:s'),
            'calMonth' => $fake->randomDigitNotNull,
            'calYear' => $fake->randomDigitNotNull,
            'isWorkingDay' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $calenderMasterFields);
    }
}
