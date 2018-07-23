<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoPaymentTermsRefferedbackApiTest extends TestCase
{
    use MakePoPaymentTermsRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->fakePoPaymentTermsRefferedbackData();
        $this->json('POST', '/api/v1/poPaymentTermsRefferedbacks', $poPaymentTermsRefferedback);

        $this->assertApiResponse($poPaymentTermsRefferedback);
    }

    /**
     * @test
     */
    public function testReadPoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->makePoPaymentTermsRefferedback();
        $this->json('GET', '/api/v1/poPaymentTermsRefferedbacks/'.$poPaymentTermsRefferedback->id);

        $this->assertApiResponse($poPaymentTermsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->makePoPaymentTermsRefferedback();
        $editedPoPaymentTermsRefferedback = $this->fakePoPaymentTermsRefferedbackData();

        $this->json('PUT', '/api/v1/poPaymentTermsRefferedbacks/'.$poPaymentTermsRefferedback->id, $editedPoPaymentTermsRefferedback);

        $this->assertApiResponse($editedPoPaymentTermsRefferedback);
    }

    /**
     * @test
     */
    public function testDeletePoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->makePoPaymentTermsRefferedback();
        $this->json('DELETE', '/api/v1/poPaymentTermsRefferedbacks/'.$poPaymentTermsRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/poPaymentTermsRefferedbacks/'.$poPaymentTermsRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
