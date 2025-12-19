<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSupplierCatalogDetailTrait;
use Tests\ApiTestTrait;

class SupplierCatalogDetailApiTest extends TestCase
{
    use MakeSupplierCatalogDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->fakeSupplierCatalogDetailData();
        $this->response = $this->json('POST', '/api/supplierCatalogDetails', $supplierCatalogDetail);

        $this->assertApiResponse($supplierCatalogDetail);
    }

    /**
     * @test
     */
    public function test_read_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->makeSupplierCatalogDetail();
        $this->response = $this->json('GET', '/api/supplierCatalogDetails/'.$supplierCatalogDetail->id);

        $this->assertApiResponse($supplierCatalogDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->makeSupplierCatalogDetail();
        $editedSupplierCatalogDetail = $this->fakeSupplierCatalogDetailData();

        $this->response = $this->json('PUT', '/api/supplierCatalogDetails/'.$supplierCatalogDetail->id, $editedSupplierCatalogDetail);

        $this->assertApiResponse($editedSupplierCatalogDetail);
    }

    /**
     * @test
     */
    public function test_delete_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->makeSupplierCatalogDetail();
        $this->response = $this->json('DELETE', '/api/supplierCatalogDetails/'.$supplierCatalogDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/supplierCatalogDetails/'.$supplierCatalogDetail->id);

        $this->response->assertStatus(404);
    }
}
