<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationVersionDetailsApiTest extends TestCase
{
    use MakeQuotationVersionDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->fakeQuotationVersionDetailsData();
        $this->json('POST', '/api/v1/quotationVersionDetails', $quotationVersionDetails);

        $this->assertApiResponse($quotationVersionDetails);
    }

    /**
     * @test
     */
    public function testReadQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->makeQuotationVersionDetails();
        $this->json('GET', '/api/v1/quotationVersionDetails/'.$quotationVersionDetails->id);

        $this->assertApiResponse($quotationVersionDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->makeQuotationVersionDetails();
        $editedQuotationVersionDetails = $this->fakeQuotationVersionDetailsData();

        $this->json('PUT', '/api/v1/quotationVersionDetails/'.$quotationVersionDetails->id, $editedQuotationVersionDetails);

        $this->assertApiResponse($editedQuotationVersionDetails);
    }

    /**
     * @test
     */
    public function testDeleteQuotationVersionDetails()
    {
        $quotationVersionDetails = $this->makeQuotationVersionDetails();
        $this->json('DELETE', '/api/v1/quotationVersionDetails/'.$quotationVersionDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quotationVersionDetails/'.$quotationVersionDetails->id);

        $this->assertResponseStatus(404);
    }
}
