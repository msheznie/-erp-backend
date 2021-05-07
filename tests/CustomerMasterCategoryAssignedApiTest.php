<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerMasterCategoryAssigned;

class CustomerMasterCategoryAssignedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_master_category_assigneds', $customerMasterCategoryAssigned
        );

        $this->assertApiResponse($customerMasterCategoryAssigned);
    }

    /**
     * @test
     */
    public function test_read_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_master_category_assigneds/'.$customerMasterCategoryAssigned->id
        );

        $this->assertApiResponse($customerMasterCategoryAssigned->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->create();
        $editedCustomerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_master_category_assigneds/'.$customerMasterCategoryAssigned->id,
            $editedCustomerMasterCategoryAssigned
        );

        $this->assertApiResponse($editedCustomerMasterCategoryAssigned);
    }

    /**
     * @test
     */
    public function test_delete_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_master_category_assigneds/'.$customerMasterCategoryAssigned->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_master_category_assigneds/'.$customerMasterCategoryAssigned->id
        );

        $this->response->assertStatus(404);
    }
}
