<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerMasterCategoryApiTest extends TestCase
{
    use MakeCustomerMasterCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerMasterCategory()
    {
        $customerMasterCategory = $this->fakeCustomerMasterCategoryData();
        $this->json('POST', '/api/v1/customerMasterCategories', $customerMasterCategory);

        $this->assertApiResponse($customerMasterCategory);
    }

    /**
     * @test
     */
    public function testReadCustomerMasterCategory()
    {
        $customerMasterCategory = $this->makeCustomerMasterCategory();
        $this->json('GET', '/api/v1/customerMasterCategories/'.$customerMasterCategory->id);

        $this->assertApiResponse($customerMasterCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerMasterCategory()
    {
        $customerMasterCategory = $this->makeCustomerMasterCategory();
        $editedCustomerMasterCategory = $this->fakeCustomerMasterCategoryData();

        $this->json('PUT', '/api/v1/customerMasterCategories/'.$customerMasterCategory->id, $editedCustomerMasterCategory);

        $this->assertApiResponse($editedCustomerMasterCategory);
    }

    /**
     * @test
     */
    public function testDeleteCustomerMasterCategory()
    {
        $customerMasterCategory = $this->makeCustomerMasterCategory();
        $this->json('DELETE', '/api/v1/customerMasterCategories/'.$customerMasterCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerMasterCategories/'.$customerMasterCategory->id);

        $this->assertResponseStatus(404);
    }
}
