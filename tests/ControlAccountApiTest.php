<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ControlAccountApiTest extends TestCase
{
    use MakeControlAccountTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateControlAccount()
    {
        $controlAccount = $this->fakeControlAccountData();
        $this->json('POST', '/api/v1/controlAccounts', $controlAccount);

        $this->assertApiResponse($controlAccount);
    }

    /**
     * @test
     */
    public function testReadControlAccount()
    {
        $controlAccount = $this->makeControlAccount();
        $this->json('GET', '/api/v1/controlAccounts/'.$controlAccount->id);

        $this->assertApiResponse($controlAccount->toArray());
    }

    /**
     * @test
     */
    public function testUpdateControlAccount()
    {
        $controlAccount = $this->makeControlAccount();
        $editedControlAccount = $this->fakeControlAccountData();

        $this->json('PUT', '/api/v1/controlAccounts/'.$controlAccount->id, $editedControlAccount);

        $this->assertApiResponse($editedControlAccount);
    }

    /**
     * @test
     */
    public function testDeleteControlAccount()
    {
        $controlAccount = $this->makeControlAccount();
        $this->json('DELETE', '/api/v1/controlAccounts/'.$controlAccount->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/controlAccounts/'.$controlAccount->id);

        $this->assertResponseStatus(404);
    }
}
