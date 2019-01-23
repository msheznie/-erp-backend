<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationDetailsApiTest extends TestCase
{
    use MakeQuotationDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuotationDetails()
    {
        $quotationDetails = $this->fakeQuotationDetailsData();
        $this->json('POST', '/api/v1/quotationDetails', $quotationDetails);

        $this->assertApiResponse($quotationDetails);
    }

    /**
     * @test
     */
    public function testReadQuotationDetails()
    {
        $quotationDetails = $this->makeQuotationDetails();
        $this->json('GET', '/api/v1/quotationDetails/'.$quotationDetails->id);

        $this->assertApiResponse($quotationDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuotationDetails()
    {
        $quotationDetails = $this->makeQuotationDetails();
        $editedQuotationDetails = $this->fakeQuotationDetailsData();

        $this->json('PUT', '/api/v1/quotationDetails/'.$quotationDetails->id, $editedQuotationDetails);

        $this->assertApiResponse($editedQuotationDetails);
    }

    /**
     * @test
     */
    public function testDeleteQuotationDetails()
    {
        $quotationDetails = $this->makeQuotationDetails();
        $this->json('DELETE', '/api/v1/quotationDetails/'.$quotationDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quotationDetails/'.$quotationDetails->id);

        $this->assertResponseStatus(404);
    }
}
