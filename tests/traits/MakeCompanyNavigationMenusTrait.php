<?php

use Faker\Factory as Faker;
use App\Models\CompanyNavigationMenus;
use App\Repositories\CompanyNavigationMenusRepository;

trait MakeCompanyNavigationMenusTrait
{
    /**
     * Create fake instance of CompanyNavigationMenus and save it in database
     *
     * @param array $companyNavigationMenusFields
     * @return CompanyNavigationMenus
     */
    public function makeCompanyNavigationMenus($companyNavigationMenusFields = [])
    {
        /** @var CompanyNavigationMenusRepository $companyNavigationMenusRepo */
        $companyNavigationMenusRepo = App::make(CompanyNavigationMenusRepository::class);
        $theme = $this->fakeCompanyNavigationMenusData($companyNavigationMenusFields);
        return $companyNavigationMenusRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyNavigationMenus
     *
     * @param array $companyNavigationMenusFields
     * @return CompanyNavigationMenus
     */
    public function fakeCompanyNavigationMenus($companyNavigationMenusFields = [])
    {
        return new CompanyNavigationMenus($this->fakeCompanyNavigationMenusData($companyNavigationMenusFields));
    }

    /**
     * Get fake data of CompanyNavigationMenus
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyNavigationMenusData($companyNavigationMenusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'companyID' => $fake->word,
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
        ], $companyNavigationMenusFields);
    }
}
