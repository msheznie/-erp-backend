<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountsReceivableLedgerApiTest extends TestCase
{
    use MakeAccountsReceivableLedgerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->fakeAccountsReceivableLedgerData();
        $this->json('POST', '/api/v1/accountsReceivableLedgers', $accountsReceivableLedger);

        $this->assertApiResponse($accountsReceivableLedger);
    }

    /**
     * @test
     */
    public function testReadAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->makeAccountsReceivableLedger();
        $this->json('GET', '/api/v1/accountsReceivableLedgers/'.$accountsReceivableLedger->id);

        $this->assertApiResponse($accountsReceivableLedger->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->makeAccountsReceivableLedger();
        $editedAccountsReceivableLedger = $this->fakeAccountsReceivableLedgerData();

        $this->json('PUT', '/api/v1/accountsReceivableLedgers/'.$accountsReceivableLedger->id, $editedAccountsReceivableLedger);

        $this->assertApiResponse($editedAccountsReceivableLedger);
    }

    /**
     * @test
     */
    public function testDeleteAccountsReceivableLedger()
    {
        $accountsReceivableLedger = $this->makeAccountsReceivableLedger();
        $this->json('DELETE', '/api/v1/accountsReceivableLedgers/'.$accountsReceivableLedger->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/accountsReceivableLedgers/'.$accountsReceivableLedger->id);

        $this->assertResponseStatus(404);
    }
}
