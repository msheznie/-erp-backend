<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationMasterVersionApiTest extends TestCase
{
    use MakeQuotationMasterVersionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->fakeQuotationMasterVersionData();
        $this->json('POST', '/api/v1/quotationMasterVersions', $quotationMasterVersion);

        $this->assertApiResponse($quotationMasterVersion);
    }

    /**
     * @test
     */
    public function testReadQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->makeQuotationMasterVersion();
        $this->json('GET', '/api/v1/quotationMasterVersions/'.$quotationMasterVersion->id);

        $this->assertApiResponse($quotationMasterVersion->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->makeQuotationMasterVersion();
        $editedQuotationMasterVersion = $this->fakeQuotationMasterVersionData();

        $this->json('PUT', '/api/v1/quotationMasterVersions/'.$quotationMasterVersion->id, $editedQuotationMasterVersion);

        $this->assertApiResponse($editedQuotationMasterVersion);
    }

    /**
     * @test
     */
    public function testDeleteQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->makeQuotationMasterVersion();
        $this->json('DELETE', '/api/v1/quotationMasterVersions/'.$quotationMasterVersion->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quotationMasterVersions/'.$quotationMasterVersion->id);

        $this->assertResponseStatus(404);
    }
}
