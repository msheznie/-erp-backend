<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierContactTypeApiTest extends TestCase
{
    use MakeSupplierContactTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierContactType()
    {
        $supplierContactType = $this->fakeSupplierContactTypeData();
        $this->json('POST', '/api/v1/supplierContactTypes', $supplierContactType);

        $this->assertApiResponse($supplierContactType);
    }

    /**
     * @test
     */
    public function testReadSupplierContactType()
    {
        $supplierContactType = $this->makeSupplierContactType();
        $this->json('GET', '/api/v1/supplierContactTypes/'.$supplierContactType->id);

        $this->assertApiResponse($supplierContactType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierContactType()
    {
        $supplierContactType = $this->makeSupplierContactType();
        $editedSupplierContactType = $this->fakeSupplierContactTypeData();

        $this->json('PUT', '/api/v1/supplierContactTypes/'.$supplierContactType->id, $editedSupplierContactType);

        $this->assertApiResponse($editedSupplierContactType);
    }

    /**
     * @test
     */
    public function testDeleteSupplierContactType()
    {
        $supplierContactType = $this->makeSupplierContactType();
        $this->json('DELETE', '/api/v1/supplierContactTypes/'.$supplierContactType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierContactTypes/'.$supplierContactType->id);

        $this->assertResponseStatus(404);
    }
}
