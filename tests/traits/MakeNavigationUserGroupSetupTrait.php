<?php

use Faker\Factory as Faker;
use App\Models\NavigationUserGroupSetup;
use App\Repositories\NavigationUserGroupSetupRepository;

trait MakeNavigationUserGroupSetupTrait
{
    /**
     * Create fake instance of NavigationUserGroupSetup and save it in database
     *
     * @param array $navigationUserGroupSetupFields
     * @return NavigationUserGroupSetup
     */
    public function makeNavigationUserGroupSetup($navigationUserGroupSetupFields = [])
    {
        /** @var NavigationUserGroupSetupRepository $navigationUserGroupSetupRepo */
        $navigationUserGroupSetupRepo = App::make(NavigationUserGroupSetupRepository::class);
        $theme = $this->fakeNavigationUserGroupSetupData($navigationUserGroupSetupFields);
        return $navigationUserGroupSetupRepo->create($theme);
    }

    /**
     * Get fake instance of NavigationUserGroupSetup
     *
     * @param array $navigationUserGroupSetupFields
     * @return NavigationUserGroupSetup
     */
    public function fakeNavigationUserGroupSetup($navigationUserGroupSetupFields = [])
    {
        return new NavigationUserGroupSetup($this->fakeNavigationUserGroupSetupData($navigationUserGroupSetupFields));
    }

    /**
     * Get fake data of NavigationUserGroupSetup
     *
     * @param array $postFields
     * @return array
     */
    public function fakeNavigationUserGroupSetupData($navigationUserGroupSetupFields = [])
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
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $navigationUserGroupSetupFields);
    }
}
