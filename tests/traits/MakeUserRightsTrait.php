<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\UserRights;
use App\Repositories\UserRightsRepository;

trait MakeUserRightsTrait
{
    /**
     * Create fake instance of UserRights and save it in database
     *
     * @param array $userRightsFields
     * @return UserRights
     */
    public function makeUserRights($userRightsFields = [])
    {
        /** @var UserRightsRepository $userRightsRepo */
        $userRightsRepo = \App::make(UserRightsRepository::class);
        $theme = $this->fakeUserRightsData($userRightsFields);
        return $userRightsRepo->create($theme);
    }

    /**
     * Get fake instance of UserRights
     *
     * @param array $userRightsFields
     * @return UserRights
     */
    public function fakeUserRights($userRightsFields = [])
    {
        return new UserRights($this->fakeUserRightsData($userRightsFields));
    }

    /**
     * Get fake data of UserRights
     *
     * @param array $userRightsFields
     * @return array
     */
    public function fakeUserRightsData($userRightsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'employeeID' => $fake->word,
            'groupMasterID' => $fake->randomDigitNotNull,
            'pageMasterID' => $fake->randomDigitNotNull,
            'moduleMasterID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'V' => $fake->randomDigitNotNull,
            'A' => $fake->randomDigitNotNull,
            'E' => $fake->randomDigitNotNull,
            'D' => $fake->randomDigitNotNull,
            'P' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $userRightsFields);
    }
}
