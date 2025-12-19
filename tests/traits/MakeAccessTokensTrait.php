<?php

use Faker\Factory as Faker;
use App\Models\AccessTokens;
use App\Repositories\AccessTokensRepository;

trait MakeAccessTokensTrait
{
    /**
     * Create fake instance of AccessTokens and save it in database
     *
     * @param array $accessTokensFields
     * @return AccessTokens
     */
    public function makeAccessTokens($accessTokensFields = [])
    {
        /** @var AccessTokensRepository $accessTokensRepo */
        $accessTokensRepo = App::make(AccessTokensRepository::class);
        $theme = $this->fakeAccessTokensData($accessTokensFields);
        return $accessTokensRepo->create($theme);
    }

    /**
     * Get fake instance of AccessTokens
     *
     * @param array $accessTokensFields
     * @return AccessTokens
     */
    public function fakeAccessTokens($accessTokensFields = [])
    {
        return new AccessTokens($this->fakeAccessTokensData($accessTokensFields));
    }

    /**
     * Get fake data of AccessTokens
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAccessTokensData($accessTokensFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'client_id' => $fake->randomDigitNotNull,
            'name' => $fake->word,
            'scopes' => $fake->text,
            'revoked' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'expires_at' => $fake->date('Y-m-d H:i:s')
        ], $accessTokensFields);
    }
}
