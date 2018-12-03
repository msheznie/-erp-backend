<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategoryICVMasterApiTest extends TestCase
{
    use MakeSupplierCategoryICVMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->fakeSupplierCategoryICVMasterData();
        $this->json('POST', '/api/v1/supplierCategoryICVMasters', $supplierCategoryICVMaster);

        $this->assertApiResponse($supplierCategoryICVMaster);
    }

    /**
     * @test
     */
    public function testReadSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->makeSupplierCategoryICVMaster();
        $this->json('GET', '/api/v1/supplierCategoryICVMasters/'.$supplierCategoryICVMaster->id);

        $this->assertApiResponse($supplierCategoryICVMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->makeSupplierCategoryICVMaster();
        $editedSupplierCategoryICVMaster = $this->fakeSupplierCategoryICVMasterData();

        $this->json('PUT', '/api/v1/supplierCategoryICVMasters/'.$supplierCategoryICVMaster->id, $editedSupplierCategoryICVMaster);

        $this->assertApiResponse($editedSupplierCategoryICVMaster);
    }

    /**
     * @test
     */
    public function testDeleteSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->makeSupplierCategoryICVMaster();
        $this->json('DELETE', '/api/v1/supplierCategoryICVMasters/'.$supplierCategoryICVMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierCategoryICVMasters/'.$supplierCategoryICVMaster->id);

        $this->assertResponseStatus(404);
    }
}
