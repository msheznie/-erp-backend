<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectInvoiceDetailsApiTest extends TestCase
{
    use MakeDirectInvoiceDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->fakeDirectInvoiceDetailsData();
        $this->json('POST', '/api/v1/directInvoiceDetails', $directInvoiceDetails);

        $this->assertApiResponse($directInvoiceDetails);
    }

    /**
     * @test
     */
    public function testReadDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->makeDirectInvoiceDetails();
        $this->json('GET', '/api/v1/directInvoiceDetails/'.$directInvoiceDetails->id);

        $this->assertApiResponse($directInvoiceDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->makeDirectInvoiceDetails();
        $editedDirectInvoiceDetails = $this->fakeDirectInvoiceDetailsData();

        $this->json('PUT', '/api/v1/directInvoiceDetails/'.$directInvoiceDetails->id, $editedDirectInvoiceDetails);

        $this->assertApiResponse($editedDirectInvoiceDetails);
    }

    /**
     * @test
     */
    public function testDeleteDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->makeDirectInvoiceDetails();
        $this->json('DELETE', '/api/v1/directInvoiceDetails/'.$directInvoiceDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/directInvoiceDetails/'.$directInvoiceDetails->id);

        $this->assertResponseStatus(404);
    }
}
