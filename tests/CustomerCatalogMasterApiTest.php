<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerCatalogMasterTrait;
use Tests\ApiTestTrait;

class CustomerCatalogMasterApiTest extends TestCase
{
    use MakeCustomerCatalogMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_catalog_master()
    {
        $customerCatalogMaster = $this->fakeCustomerCatalogMasterData();
        $this->response = $this->json('POST', '/api/customerCatalogMasters', $customerCatalogMaster);

        $this->assertApiResponse($customerCatalogMaster);
    }

    /**
     * @test
     */
    public function test_read_customer_catalog_master()
    {
        $customerCatalogMaster = $this->makeCustomerCatalogMaster();
        $this->response = $this->json('GET', '/api/customerCatalogMasters/'.$customerCatalogMaster->id);

        $this->assertApiResponse($customerCatalogMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_catalog_master()
    {
        $customerCatalogMaster = $this->makeCustomerCatalogMaster();
        $editedCustomerCatalogMaster = $this->fakeCustomerCatalogMasterData();

        $this->response = $this->json('PUT', '/api/customerCatalogMasters/'.$customerCatalogMaster->id, $editedCustomerCatalogMaster);

        $this->assertApiResponse($editedCustomerCatalogMaster);
    }

    /**
     * @test
     */
    public function test_delete_customer_catalog_master()
    {
        $customerCatalogMaster = $this->makeCustomerCatalogMaster();
        $this->response = $this->json('DELETE', '/api/customerCatalogMasters/'.$customerCatalogMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/customerCatalogMasters/'.$customerCatalogMaster->id);

        $this->response->assertStatus(404);
    }
}
