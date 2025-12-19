<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierTypeApiTest extends TestCase
{
    use MakeSupplierTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierType()
    {
        $supplierType = $this->fakeSupplierTypeData();
        $this->json('POST', '/api/v1/supplierTypes', $supplierType);

        $this->assertApiResponse($supplierType);
    }

    /**
     * @test
     */
    public function testReadSupplierType()
    {
        $supplierType = $this->makeSupplierType();
        $this->json('GET', '/api/v1/supplierTypes/'.$supplierType->id);

        $this->assertApiResponse($supplierType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierType()
    {
        $supplierType = $this->makeSupplierType();
        $editedSupplierType = $this->fakeSupplierTypeData();

        $this->json('PUT', '/api/v1/supplierTypes/'.$supplierType->id, $editedSupplierType);

        $this->assertApiResponse($editedSupplierType);
    }

    /**
     * @test
     */
    public function testDeleteSupplierType()
    {
        $supplierType = $this->makeSupplierType();
        $this->json('DELETE', '/api/v1/supplierTypes/'.$supplierType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierTypes/'.$supplierType->id);

        $this->assertResponseStatus(404);
    }
}
