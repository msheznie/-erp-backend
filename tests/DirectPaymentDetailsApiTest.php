<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectPaymentDetailsApiTest extends TestCase
{
    use MakeDirectPaymentDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDirectPaymentDetails()
    {
        $directPaymentDetails = $this->fakeDirectPaymentDetailsData();
        $this->json('POST', '/api/v1/directPaymentDetails', $directPaymentDetails);

        $this->assertApiResponse($directPaymentDetails);
    }

    /**
     * @test
     */
    public function testReadDirectPaymentDetails()
    {
        $directPaymentDetails = $this->makeDirectPaymentDetails();
        $this->json('GET', '/api/v1/directPaymentDetails/'.$directPaymentDetails->id);

        $this->assertApiResponse($directPaymentDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDirectPaymentDetails()
    {
        $directPaymentDetails = $this->makeDirectPaymentDetails();
        $editedDirectPaymentDetails = $this->fakeDirectPaymentDetailsData();

        $this->json('PUT', '/api/v1/directPaymentDetails/'.$directPaymentDetails->id, $editedDirectPaymentDetails);

        $this->assertApiResponse($editedDirectPaymentDetails);
    }

    /**
     * @test
     */
    public function testDeleteDirectPaymentDetails()
    {
        $directPaymentDetails = $this->makeDirectPaymentDetails();
        $this->json('DELETE', '/api/v1/directPaymentDetails/'.$directPaymentDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/directPaymentDetails/'.$directPaymentDetails->id);

        $this->assertResponseStatus(404);
    }
}
