<?php

use Faker\Factory as Faker;
use App\Models\UsersLogHistory;
use App\Repositories\UsersLogHistoryRepository;

trait MakeUsersLogHistoryTrait
{
    /**
     * Create fake instance of UsersLogHistory and save it in database
     *
     * @param array $usersLogHistoryFields
     * @return UsersLogHistory
     */
    public function makeUsersLogHistory($usersLogHistoryFields = [])
    {
        /** @var UsersLogHistoryRepository $usersLogHistoryRepo */
        $usersLogHistoryRepo = App::make(UsersLogHistoryRepository::class);
        $theme = $this->fakeUsersLogHistoryData($usersLogHistoryFields);
        return $usersLogHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of UsersLogHistory
     *
     * @param array $usersLogHistoryFields
     * @return UsersLogHistory
     */
    public function fakeUsersLogHistory($usersLogHistoryFields = [])
    {
        return new UsersLogHistory($this->fakeUsersLogHistoryData($usersLogHistoryFields));
    }

    /**
     * Get fake data of UsersLogHistory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUsersLogHistoryData($usersLogHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'employee_id' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'loginPCId' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $usersLogHistoryFields);
    }
}
