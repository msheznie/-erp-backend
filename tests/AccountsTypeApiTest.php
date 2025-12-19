<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountsTypeApiTest extends TestCase
{
    use MakeAccountsTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAccountsType()
    {
        $accountsType = $this->fakeAccountsTypeData();
        $this->json('POST', '/api/v1/accountsTypes', $accountsType);

        $this->assertApiResponse($accountsType);
    }

    /**
     * @test
     */
    public function testReadAccountsType()
    {
        $accountsType = $this->makeAccountsType();
        $this->json('GET', '/api/v1/accountsTypes/'.$accountsType->id);

        $this->assertApiResponse($accountsType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAccountsType()
    {
        $accountsType = $this->makeAccountsType();
        $editedAccountsType = $this->fakeAccountsTypeData();

        $this->json('PUT', '/api/v1/accountsTypes/'.$accountsType->id, $editedAccountsType);

        $this->assertApiResponse($editedAccountsType);
    }

    /**
     * @test
     */
    public function testDeleteAccountsType()
    {
        $accountsType = $this->makeAccountsType();
        $this->json('DELETE', '/api/v1/accountsTypes/'.$accountsType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/accountsTypes/'.$accountsType->id);

        $this->assertResponseStatus(404);
    }
}
