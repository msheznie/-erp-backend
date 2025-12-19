<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccessTokensApiTest extends TestCase
{
    use MakeAccessTokensTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAccessTokens()
    {
        $accessTokens = $this->fakeAccessTokensData();
        $this->json('POST', '/api/v1/accessTokens', $accessTokens);

        $this->assertApiResponse($accessTokens);
    }

    /**
     * @test
     */
    public function testReadAccessTokens()
    {
        $accessTokens = $this->makeAccessTokens();
        $this->json('GET', '/api/v1/accessTokens/'.$accessTokens->id);

        $this->assertApiResponse($accessTokens->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAccessTokens()
    {
        $accessTokens = $this->makeAccessTokens();
        $editedAccessTokens = $this->fakeAccessTokensData();

        $this->json('PUT', '/api/v1/accessTokens/'.$accessTokens->id, $editedAccessTokens);

        $this->assertApiResponse($editedAccessTokens);
    }

    /**
     * @test
     */
    public function testDeleteAccessTokens()
    {
        $accessTokens = $this->makeAccessTokens();
        $this->json('DELETE', '/api/v1/accessTokens/'.$accessTokens->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/accessTokens/'.$accessTokens->id);

        $this->assertResponseStatus(404);
    }
}
