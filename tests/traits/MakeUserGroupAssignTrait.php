<?php

use Faker\Factory as Faker;
use App\Models\UserGroupAssign;
use App\Repositories\UserGroupAssignRepository;

trait MakeUserGroupAssignTrait
{
    /**
     * Create fake instance of UserGroupAssign and save it in database
     *
     * @param array $userGroupAssignFields
     * @return UserGroupAssign
     */
    public function makeUserGroupAssign($userGroupAssignFields = [])
    {
        /** @var UserGroupAssignRepository $userGroupAssignRepo */
        $userGroupAssignRepo = App::make(UserGroupAssignRepository::class);
        $theme = $this->fakeUserGroupAssignData($userGroupAssignFields);
        return $userGroupAssignRepo->create($theme);
    }

    /**
     * Get fake instance of UserGroupAssign
     *
     * @param array $userGroupAssignFields
     * @return UserGroupAssign
     */
    public function fakeUserGroupAssign($userGroupAssignFields = [])
    {
        return new UserGroupAssign($this->fakeUserGroupAssignData($userGroupAssignFields));
    }

    /**
     * Get fake data of UserGroupAssign
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUserGroupAssignData($userGroupAssignFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'userGroupID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'navigationMenuID' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'masterID' => $fake->randomDigitNotNull,
            'url' => $fake->text,
            'pageID' => $fake->word,
            'pageTitle' => $fake->word,
            'pageIcon' => $fake->word,
            'levelNo' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'isSubExist' => $fake->randomDigitNotNull,
            'readonly' => $fake->word,
            'create' => $fake->word,
            'update' => $fake->word,
            'delete' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $userGroupAssignFields);
    }
}
