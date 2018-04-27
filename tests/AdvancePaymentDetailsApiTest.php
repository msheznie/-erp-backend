<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdvancePaymentDetailsApiTest extends TestCase
{
    use MakeAdvancePaymentDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->fakeAdvancePaymentDetailsData();
        $this->json('POST', '/api/v1/advancePaymentDetails', $advancePaymentDetails);

        $this->assertApiResponse($advancePaymentDetails);
    }

    /**
     * @test
     */
    public function testReadAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->makeAdvancePaymentDetails();
        $this->json('GET', '/api/v1/advancePaymentDetails/'.$advancePaymentDetails->id);

        $this->assertApiResponse($advancePaymentDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->makeAdvancePaymentDetails();
        $editedAdvancePaymentDetails = $this->fakeAdvancePaymentDetailsData();

        $this->json('PUT', '/api/v1/advancePaymentDetails/'.$advancePaymentDetails->id, $editedAdvancePaymentDetails);

        $this->assertApiResponse($editedAdvancePaymentDetails);
    }

    /**
     * @test
     */
    public function testDeleteAdvancePaymentDetails()
    {
        $advancePaymentDetails = $this->makeAdvancePaymentDetails();
        $this->json('DELETE', '/api/v1/advancePaymentDetails/'.$advancePaymentDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/advancePaymentDetails/'.$advancePaymentDetails->id);

        $this->assertResponseStatus(404);
    }
}
