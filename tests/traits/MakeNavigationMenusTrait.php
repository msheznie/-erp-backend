<?php

use Faker\Factory as Faker;
use App\Models\NavigationMenus;
use App\Repositories\NavigationMenusRepository;

trait MakeNavigationMenusTrait
{
    /**
     * Create fake instance of NavigationMenus and save it in database
     *
     * @param array $navigationMenusFields
     * @return NavigationMenus
     */
    public function makeNavigationMenus($navigationMenusFields = [])
    {
        /** @var NavigationMenusRepository $navigationMenusRepo */
        $navigationMenusRepo = App::make(NavigationMenusRepository::class);
        $theme = $this->fakeNavigationMenusData($navigationMenusFields);
        return $navigationMenusRepo->create($theme);
    }

    /**
     * Get fake instance of NavigationMenus
     *
     * @param array $navigationMenusFields
     * @return NavigationMenus
     */
    public function fakeNavigationMenus($navigationMenusFields = [])
    {
        return new NavigationMenus($this->fakeNavigationMenusData($navigationMenusFields));
    }

    /**
     * Get fake data of NavigationMenus
     *
     * @param array $postFields
     * @return array
     */
    public function fakeNavigationMenusData($navigationMenusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'masterID' => $fake->randomDigitNotNull,
            'languageID' => $fake->randomDigitNotNull,
            'url' => $fake->text,
            'pageID' => $fake->word,
            'pageTitle' => $fake->word,
            'pageIcon' => $fake->word,
            'levelNo' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'isSubExist' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'isAddon' => $fake->randomDigitNotNull,
            'addonDescription' => $fake->word,
            'addonDetails' => $fake->text,
            'isCoreModule' => $fake->randomDigitNotNull,
            'isGroup' => $fake->randomDigitNotNull
        ], $navigationMenusFields);
    }
}
