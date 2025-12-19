<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerCatalogDetailTrait;
use Tests\ApiTestTrait;

class CustomerCatalogDetailApiTest extends TestCase
{
    use MakeCustomerCatalogDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->fakeCustomerCatalogDetailData();
        $this->response = $this->json('POST', '/api/customerCatalogDetails', $customerCatalogDetail);

        $this->assertApiResponse($customerCatalogDetail);
    }

    /**
     * @test
     */
    public function test_read_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->makeCustomerCatalogDetail();
        $this->response = $this->json('GET', '/api/customerCatalogDetails/'.$customerCatalogDetail->id);

        $this->assertApiResponse($customerCatalogDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->makeCustomerCatalogDetail();
        $editedCustomerCatalogDetail = $this->fakeCustomerCatalogDetailData();

        $this->response = $this->json('PUT', '/api/customerCatalogDetails/'.$customerCatalogDetail->id, $editedCustomerCatalogDetail);

        $this->assertApiResponse($editedCustomerCatalogDetail);
    }

    /**
     * @test
     */
    public function test_delete_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->makeCustomerCatalogDetail();
        $this->response = $this->json('DELETE', '/api/customerCatalogDetails/'.$customerCatalogDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/customerCatalogDetails/'.$customerCatalogDetail->id);

        $this->response->assertStatus(404);
    }
}
