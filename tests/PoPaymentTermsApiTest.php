<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoPaymentTermsApiTest extends TestCase
{
    use MakePoPaymentTermsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePoPaymentTerms()
    {
        $poPaymentTerms = $this->fakePoPaymentTermsData();
        $this->json('POST', '/api/v1/poPaymentTerms', $poPaymentTerms);

        $this->assertApiResponse($poPaymentTerms);
    }

    /**
     * @test
     */
    public function testReadPoPaymentTerms()
    {
        $poPaymentTerms = $this->makePoPaymentTerms();
        $this->json('GET', '/api/v1/poPaymentTerms/'.$poPaymentTerms->id);

        $this->assertApiResponse($poPaymentTerms->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePoPaymentTerms()
    {
        $poPaymentTerms = $this->makePoPaymentTerms();
        $editedPoPaymentTerms = $this->fakePoPaymentTermsData();

        $this->json('PUT', '/api/v1/poPaymentTerms/'.$poPaymentTerms->id, $editedPoPaymentTerms);

        $this->assertApiResponse($editedPoPaymentTerms);
    }

    /**
     * @test
     */
    public function testDeletePoPaymentTerms()
    {
        $poPaymentTerms = $this->makePoPaymentTerms();
        $this->json('DELETE', '/api/v1/poPaymentTerms/'.$poPaymentTerms->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/poPaymentTerms/'.$poPaymentTerms->id);

        $this->assertResponseStatus(404);
    }
}
