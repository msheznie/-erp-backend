<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectPaymentReferbackApiTest extends TestCase
{
    use MakeDirectPaymentReferbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDirectPaymentReferback()
    {
        $directPaymentReferback = $this->fakeDirectPaymentReferbackData();
        $this->json('POST', '/api/v1/directPaymentReferbacks', $directPaymentReferback);

        $this->assertApiResponse($directPaymentReferback);
    }

    /**
     * @test
     */
    public function testReadDirectPaymentReferback()
    {
        $directPaymentReferback = $this->makeDirectPaymentReferback();
        $this->json('GET', '/api/v1/directPaymentReferbacks/'.$directPaymentReferback->id);

        $this->assertApiResponse($directPaymentReferback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDirectPaymentReferback()
    {
        $directPaymentReferback = $this->makeDirectPaymentReferback();
        $editedDirectPaymentReferback = $this->fakeDirectPaymentReferbackData();

        $this->json('PUT', '/api/v1/directPaymentReferbacks/'.$directPaymentReferback->id, $editedDirectPaymentReferback);

        $this->assertApiResponse($editedDirectPaymentReferback);
    }

    /**
     * @test
     */
    public function testDeleteDirectPaymentReferback()
    {
        $directPaymentReferback = $this->makeDirectPaymentReferback();
        $this->json('DELETE', '/api/v1/directPaymentReferbacks/'.$directPaymentReferback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/directPaymentReferbacks/'.$directPaymentReferback->id);

        $this->assertResponseStatus(404);
    }
}
