<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSupplierCatalogMasterTrait;
use Tests\ApiTestTrait;

class SupplierCatalogMasterApiTest extends TestCase
{
    use MakeSupplierCatalogMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->fakeSupplierCatalogMasterData();
        $this->response = $this->json('POST', '/api/supplierCatalogMasters', $supplierCatalogMaster);

        $this->assertApiResponse($supplierCatalogMaster);
    }

    /**
     * @test
     */
    public function test_read_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->makeSupplierCatalogMaster();
        $this->response = $this->json('GET', '/api/supplierCatalogMasters/'.$supplierCatalogMaster->id);

        $this->assertApiResponse($supplierCatalogMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->makeSupplierCatalogMaster();
        $editedSupplierCatalogMaster = $this->fakeSupplierCatalogMasterData();

        $this->response = $this->json('PUT', '/api/supplierCatalogMasters/'.$supplierCatalogMaster->id, $editedSupplierCatalogMaster);

        $this->assertApiResponse($editedSupplierCatalogMaster);
    }

    /**
     * @test
     */
    public function test_delete_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->makeSupplierCatalogMaster();
        $this->response = $this->json('DELETE', '/api/supplierCatalogMasters/'.$supplierCatalogMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/supplierCatalogMasters/'.$supplierCatalogMaster->id);

        $this->response->assertStatus(404);
    }
}
