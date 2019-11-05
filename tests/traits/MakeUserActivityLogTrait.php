<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\UserActivityLog;
use App\Repositories\UserActivityLogRepository;

trait MakeUserActivityLogTrait
{
    /**
     * Create fake instance of UserActivityLog and save it in database
     *
     * @param array $userActivityLogFields
     * @return UserActivityLog
     */
    public function makeUserActivityLog($userActivityLogFields = [])
    {
        /** @var UserActivityLogRepository $userActivityLogRepo */
        $userActivityLogRepo = \App::make(UserActivityLogRepository::class);
        $theme = $this->fakeUserActivityLogData($userActivityLogFields);
        return $userActivityLogRepo->create($theme);
    }

    /**
     * Get fake instance of UserActivityLog
     *
     * @param array $userActivityLogFields
     * @return UserActivityLog
     */
    public function fakeUserActivityLog($userActivityLogFields = [])
    {
        return new UserActivityLog($this->fakeUserActivityLogData($userActivityLogFields));
    }

    /**
     * Get fake data of UserActivityLog
     *
     * @param array $userActivityLogFields
     * @return array
     */
    public function fakeUserActivityLogData($userActivityLogFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'document_id' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'previous_value' => $fake->word,
            'current_value' => $fake->word,
            'activity_at' => $fake->date('Y-m-d H:i:s'),
            'user_pc' => $fake->word
        ], $userActivityLogFields);
    }
}
