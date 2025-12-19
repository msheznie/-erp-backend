<?php

use Faker\Factory as Faker;
use App\Models\ControlAccount;
use App\Repositories\ControlAccountRepository;

trait MakeControlAccountTrait
{
    /**
     * Create fake instance of ControlAccount and save it in database
     *
     * @param array $controlAccountFields
     * @return ControlAccount
     */
    public function makeControlAccount($controlAccountFields = [])
    {
        /** @var ControlAccountRepository $controlAccountRepo */
        $controlAccountRepo = App::make(ControlAccountRepository::class);
        $theme = $this->fakeControlAccountData($controlAccountFields);
        return $controlAccountRepo->create($theme);
    }

    /**
     * Get fake instance of ControlAccount
     *
     * @param array $controlAccountFields
     * @return ControlAccount
     */
    public function fakeControlAccount($controlAccountFields = [])
    {
        return new ControlAccount($this->fakeControlAccountData($controlAccountFields));
    }

    /**
     * Get fake data of ControlAccount
     *
     * @param array $postFields
     * @return array
     */
    public function fakeControlAccountData($controlAccountFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'controlAccountCode' => $fake->word,
            'description' => $fake->word,
            'itemLedgerShymbol' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $controlAccountFields);
    }
}
