<?php

use Faker\Factory as Faker;
use App\Models\AccountsType;
use App\Repositories\AccountsTypeRepository;

trait MakeAccountsTypeTrait
{
    /**
     * Create fake instance of AccountsType and save it in database
     *
     * @param array $accountsTypeFields
     * @return AccountsType
     */
    public function makeAccountsType($accountsTypeFields = [])
    {
        /** @var AccountsTypeRepository $accountsTypeRepo */
        $accountsTypeRepo = App::make(AccountsTypeRepository::class);
        $theme = $this->fakeAccountsTypeData($accountsTypeFields);
        return $accountsTypeRepo->create($theme);
    }

    /**
     * Get fake instance of AccountsType
     *
     * @param array $accountsTypeFields
     * @return AccountsType
     */
    public function fakeAccountsType($accountsTypeFields = [])
    {
        return new AccountsType($this->fakeAccountsTypeData($accountsTypeFields));
    }

    /**
     * Get fake data of AccountsType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAccountsTypeData($accountsTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'code' => $fake->word
        ], $accountsTypeFields);
    }
}
