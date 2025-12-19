<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ScheduleMaster;
use App\Repositories\ScheduleMasterRepository;

trait MakeScheduleMasterTrait
{
    /**
     * Create fake instance of ScheduleMaster and save it in database
     *
     * @param array $scheduleMasterFields
     * @return ScheduleMaster
     */
    public function makeScheduleMaster($scheduleMasterFields = [])
    {
        /** @var ScheduleMasterRepository $scheduleMasterRepo */
        $scheduleMasterRepo = \App::make(ScheduleMasterRepository::class);
        $theme = $this->fakeScheduleMasterData($scheduleMasterFields);
        return $scheduleMasterRepo->create($theme);
    }

    /**
     * Get fake instance of ScheduleMaster
     *
     * @param array $scheduleMasterFields
     * @return ScheduleMaster
     */
    public function fakeScheduleMaster($scheduleMasterFields = [])
    {
        return new ScheduleMaster($this->fakeScheduleMasterData($scheduleMasterFields));
    }

    /**
     * Get fake data of ScheduleMaster
     *
     * @param array $scheduleMasterFields
     * @return array
     */
    public function fakeScheduleMasterData($scheduleMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'scheduleCode' => $fake->word,
            'scheduleDescription' => $fake->word,
            'leavesEntitled' => $fake->randomDigitNotNull,
            'noofTickets' => $fake->randomDigitNotNull,
            'calculateCalendarDays' => $fake->randomDigitNotNull,
            'is13MonthApplicable' => $fake->word,
            'createDate' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPCid' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $scheduleMasterFields);
    }
}
