<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationMasterApiTest extends TestCase
{
    use MakeQuotationMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuotationMaster()
    {
        $quotationMaster = $this->fakeQuotationMasterData();
        $this->json('POST', '/api/v1/quotationMasters', $quotationMaster);

        $this->assertApiResponse($quotationMaster);
    }

    /**
     * @test
     */
    public function testReadQuotationMaster()
    {
        $quotationMaster = $this->makeQuotationMaster();
        $this->json('GET', '/api/v1/quotationMasters/'.$quotationMaster->id);

        $this->assertApiResponse($quotationMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuotationMaster()
    {
        $quotationMaster = $this->makeQuotationMaster();
        $editedQuotationMaster = $this->fakeQuotationMasterData();

        $this->json('PUT', '/api/v1/quotationMasters/'.$quotationMaster->id, $editedQuotationMaster);

        $this->assertApiResponse($editedQuotationMaster);
    }

    /**
     * @test
     */
    public function testDeleteQuotationMaster()
    {
        $quotationMaster = $this->makeQuotationMaster();
        $this->json('DELETE', '/api/v1/quotationMasters/'.$quotationMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quotationMasters/'.$quotationMaster->id);

        $this->assertResponseStatus(404);
    }
}
