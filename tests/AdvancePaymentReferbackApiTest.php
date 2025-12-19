<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdvancePaymentReferbackApiTest extends TestCase
{
    use MakeAdvancePaymentReferbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->fakeAdvancePaymentReferbackData();
        $this->json('POST', '/api/v1/advancePaymentReferbacks', $advancePaymentReferback);

        $this->assertApiResponse($advancePaymentReferback);
    }

    /**
     * @test
     */
    public function testReadAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->makeAdvancePaymentReferback();
        $this->json('GET', '/api/v1/advancePaymentReferbacks/'.$advancePaymentReferback->id);

        $this->assertApiResponse($advancePaymentReferback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->makeAdvancePaymentReferback();
        $editedAdvancePaymentReferback = $this->fakeAdvancePaymentReferbackData();

        $this->json('PUT', '/api/v1/advancePaymentReferbacks/'.$advancePaymentReferback->id, $editedAdvancePaymentReferback);

        $this->assertApiResponse($editedAdvancePaymentReferback);
    }

    /**
     * @test
     */
    public function testDeleteAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->makeAdvancePaymentReferback();
        $this->json('DELETE', '/api/v1/advancePaymentReferbacks/'.$advancePaymentReferback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/advancePaymentReferbacks/'.$advancePaymentReferback->id);

        $this->assertResponseStatus(404);
    }
}
