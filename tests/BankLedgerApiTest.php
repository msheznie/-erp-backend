<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BankLedgerApiTest extends TestCase
{
    use MakeBankLedgerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBankLedger()
    {
        $bankLedger = $this->fakeBankLedgerData();
        $this->json('POST', '/api/v1/bankLedgers', $bankLedger);

        $this->assertApiResponse($bankLedger);
    }

    /**
     * @test
     */
    public function testReadBankLedger()
    {
        $bankLedger = $this->makeBankLedger();
        $this->json('GET', '/api/v1/bankLedgers/'.$bankLedger->id);

        $this->assertApiResponse($bankLedger->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBankLedger()
    {
        $bankLedger = $this->makeBankLedger();
        $editedBankLedger = $this->fakeBankLedgerData();

        $this->json('PUT', '/api/v1/bankLedgers/'.$bankLedger->id, $editedBankLedger);

        $this->assertApiResponse($editedBankLedger);
    }

    /**
     * @test
     */
    public function testDeleteBankLedger()
    {
        $bankLedger = $this->makeBankLedger();
        $this->json('DELETE', '/api/v1/bankLedgers/'.$bankLedger->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bankLedgers/'.$bankLedger->id);

        $this->assertResponseStatus(404);
    }
}
