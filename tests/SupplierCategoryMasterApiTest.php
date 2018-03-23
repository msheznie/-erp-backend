<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategoryMasterApiTest extends TestCase
{
    use MakeSupplierCategoryMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->fakeSupplierCategoryMasterData();
        $this->json('POST', '/api/v1/supplierCategoryMasters', $supplierCategoryMaster);

        $this->assertApiResponse($supplierCategoryMaster);
    }

    /**
     * @test
     */
    public function testReadSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->makeSupplierCategoryMaster();
        $this->json('GET', '/api/v1/supplierCategoryMasters/'.$supplierCategoryMaster->id);

        $this->assertApiResponse($supplierCategoryMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->makeSupplierCategoryMaster();
        $editedSupplierCategoryMaster = $this->fakeSupplierCategoryMasterData();

        $this->json('PUT', '/api/v1/supplierCategoryMasters/'.$supplierCategoryMaster->id, $editedSupplierCategoryMaster);

        $this->assertApiResponse($editedSupplierCategoryMaster);
    }

    /**
     * @test
     */
    public function testDeleteSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->makeSupplierCategoryMaster();
        $this->json('DELETE', '/api/v1/supplierCategoryMasters/'.$supplierCategoryMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierCategoryMasters/'.$supplierCategoryMaster->id);

        $this->assertResponseStatus(404);
    }
}
