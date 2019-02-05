<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationDetailsRefferedbackApiTest extends TestCase
{
    use MakeQuotationDetailsRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->fakeQuotationDetailsRefferedbackData();
        $this->json('POST', '/api/v1/quotationDetailsRefferedbacks', $quotationDetailsRefferedback);

        $this->assertApiResponse($quotationDetailsRefferedback);
    }

    /**
     * @test
     */
    public function testReadQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->makeQuotationDetailsRefferedback();
        $this->json('GET', '/api/v1/quotationDetailsRefferedbacks/'.$quotationDetailsRefferedback->id);

        $this->assertApiResponse($quotationDetailsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->makeQuotationDetailsRefferedback();
        $editedQuotationDetailsRefferedback = $this->fakeQuotationDetailsRefferedbackData();

        $this->json('PUT', '/api/v1/quotationDetailsRefferedbacks/'.$quotationDetailsRefferedback->id, $editedQuotationDetailsRefferedback);

        $this->assertApiResponse($editedQuotationDetailsRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteQuotationDetailsRefferedback()
    {
        $quotationDetailsRefferedback = $this->makeQuotationDetailsRefferedback();
        $this->json('DELETE', '/api/v1/quotationDetailsRefferedbacks/'.$quotationDetailsRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quotationDetailsRefferedbacks/'.$quotationDetailsRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
