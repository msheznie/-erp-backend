<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierContactDetailsApiTest extends TestCase
{
    use MakeSupplierContactDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierContactDetails()
    {
        $supplierContactDetails = $this->fakeSupplierContactDetailsData();
        $this->json('POST', '/api/v1/supplierContactDetails', $supplierContactDetails);

        $this->assertApiResponse($supplierContactDetails);
    }

    /**
     * @test
     */
    public function testReadSupplierContactDetails()
    {
        $supplierContactDetails = $this->makeSupplierContactDetails();
        $this->json('GET', '/api/v1/supplierContactDetails/'.$supplierContactDetails->id);

        $this->assertApiResponse($supplierContactDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierContactDetails()
    {
        $supplierContactDetails = $this->makeSupplierContactDetails();
        $editedSupplierContactDetails = $this->fakeSupplierContactDetailsData();

        $this->json('PUT', '/api/v1/supplierContactDetails/'.$supplierContactDetails->id, $editedSupplierContactDetails);

        $this->assertApiResponse($editedSupplierContactDetails);
    }

    /**
     * @test
     */
    public function testDeleteSupplierContactDetails()
    {
        $supplierContactDetails = $this->makeSupplierContactDetails();
        $this->json('DELETE', '/api/v1/supplierContactDetails/'.$supplierContactDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierContactDetails/'.$supplierContactDetails->id);

        $this->assertResponseStatus(404);
    }
}
