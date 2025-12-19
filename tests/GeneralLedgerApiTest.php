<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeneralLedgerApiTest extends TestCase
{
    use MakeGeneralLedgerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGeneralLedger()
    {
        $generalLedger = $this->fakeGeneralLedgerData();
        $this->json('POST', '/api/v1/generalLedgers', $generalLedger);

        $this->assertApiResponse($generalLedger);
    }

    /**
     * @test
     */
    public function testReadGeneralLedger()
    {
        $generalLedger = $this->makeGeneralLedger();
        $this->json('GET', '/api/v1/generalLedgers/'.$generalLedger->id);

        $this->assertApiResponse($generalLedger->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGeneralLedger()
    {
        $generalLedger = $this->makeGeneralLedger();
        $editedGeneralLedger = $this->fakeGeneralLedgerData();

        $this->json('PUT', '/api/v1/generalLedgers/'.$generalLedger->id, $editedGeneralLedger);

        $this->assertApiResponse($editedGeneralLedger);
    }

    /**
     * @test
     */
    public function testDeleteGeneralLedger()
    {
        $generalLedger = $this->makeGeneralLedger();
        $this->json('DELETE', '/api/v1/generalLedgers/'.$generalLedger->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/generalLedgers/'.$generalLedger->id);

        $this->assertResponseStatus(404);
    }
}
