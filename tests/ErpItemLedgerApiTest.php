<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErpItemLedgerApiTest extends TestCase
{
    use MakeErpItemLedgerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateErpItemLedger()
    {
        $erpItemLedger = $this->fakeErpItemLedgerData();
        $this->json('POST', '/api/v1/erpItemLedgers', $erpItemLedger);

        $this->assertApiResponse($erpItemLedger);
    }

    /**
     * @test
     */
    public function testReadErpItemLedger()
    {
        $erpItemLedger = $this->makeErpItemLedger();
        $this->json('GET', '/api/v1/erpItemLedgers/'.$erpItemLedger->id);

        $this->assertApiResponse($erpItemLedger->toArray());
    }

    /**
     * @test
     */
    public function testUpdateErpItemLedger()
    {
        $erpItemLedger = $this->makeErpItemLedger();
        $editedErpItemLedger = $this->fakeErpItemLedgerData();

        $this->json('PUT', '/api/v1/erpItemLedgers/'.$erpItemLedger->id, $editedErpItemLedger);

        $this->assertApiResponse($editedErpItemLedger);
    }

    /**
     * @test
     */
    public function testDeleteErpItemLedger()
    {
        $erpItemLedger = $this->makeErpItemLedger();
        $this->json('DELETE', '/api/v1/erpItemLedgers/'.$erpItemLedger->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/erpItemLedgers/'.$erpItemLedger->id);

        $this->assertResponseStatus(404);
    }
}
