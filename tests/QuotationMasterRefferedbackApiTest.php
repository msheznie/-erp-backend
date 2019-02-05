<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationMasterRefferedbackApiTest extends TestCase
{
    use MakeQuotationMasterRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->fakeQuotationMasterRefferedbackData();
        $this->json('POST', '/api/v1/quotationMasterRefferedbacks', $quotationMasterRefferedback);

        $this->assertApiResponse($quotationMasterRefferedback);
    }

    /**
     * @test
     */
    public function testReadQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->makeQuotationMasterRefferedback();
        $this->json('GET', '/api/v1/quotationMasterRefferedbacks/'.$quotationMasterRefferedback->id);

        $this->assertApiResponse($quotationMasterRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->makeQuotationMasterRefferedback();
        $editedQuotationMasterRefferedback = $this->fakeQuotationMasterRefferedbackData();

        $this->json('PUT', '/api/v1/quotationMasterRefferedbacks/'.$quotationMasterRefferedback->id, $editedQuotationMasterRefferedback);

        $this->assertApiResponse($editedQuotationMasterRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteQuotationMasterRefferedback()
    {
        $quotationMasterRefferedback = $this->makeQuotationMasterRefferedback();
        $this->json('DELETE', '/api/v1/quotationMasterRefferedbacks/'.$quotationMasterRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quotationMasterRefferedbacks/'.$quotationMasterRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
