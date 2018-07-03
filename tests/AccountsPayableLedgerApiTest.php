<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AccountsPayableLedgerApiTest extends TestCase
{
    use MakeAccountsPayableLedgerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->fakeAccountsPayableLedgerData();
        $this->json('POST', '/api/v1/accountsPayableLedgers', $accountsPayableLedger);

        $this->assertApiResponse($accountsPayableLedger);
    }

    /**
     * @test
     */
    public function testReadAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->makeAccountsPayableLedger();
        $this->json('GET', '/api/v1/accountsPayableLedgers/'.$accountsPayableLedger->id);

        $this->assertApiResponse($accountsPayableLedger->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->makeAccountsPayableLedger();
        $editedAccountsPayableLedger = $this->fakeAccountsPayableLedgerData();

        $this->json('PUT', '/api/v1/accountsPayableLedgers/'.$accountsPayableLedger->id, $editedAccountsPayableLedger);

        $this->assertApiResponse($editedAccountsPayableLedger);
    }

    /**
     * @test
     */
    public function testDeleteAccountsPayableLedger()
    {
        $accountsPayableLedger = $this->makeAccountsPayableLedger();
        $this->json('DELETE', '/api/v1/accountsPayableLedgers/'.$accountsPayableLedger->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/accountsPayableLedgers/'.$accountsPayableLedger->id);

        $this->assertResponseStatus(404);
    }
}
