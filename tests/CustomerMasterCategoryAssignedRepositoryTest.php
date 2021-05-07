<?php namespace Tests\Repositories;

use App\Models\CustomerMasterCategoryAssigned;
use App\Repositories\CustomerMasterCategoryAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerMasterCategoryAssignedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerMasterCategoryAssignedRepository
     */
    protected $customerMasterCategoryAssignedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerMasterCategoryAssignedRepo = \App::make(CustomerMasterCategoryAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->make()->toArray();

        $createdCustomerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepo->create($customerMasterCategoryAssigned);

        $createdCustomerMasterCategoryAssigned = $createdCustomerMasterCategoryAssigned->toArray();
        $this->assertArrayHasKey('id', $createdCustomerMasterCategoryAssigned);
        $this->assertNotNull($createdCustomerMasterCategoryAssigned['id'], 'Created CustomerMasterCategoryAssigned must have id specified');
        $this->assertNotNull(CustomerMasterCategoryAssigned::find($createdCustomerMasterCategoryAssigned['id']), 'CustomerMasterCategoryAssigned with given id must be in DB');
        $this->assertModelData($customerMasterCategoryAssigned, $createdCustomerMasterCategoryAssigned);
    }

    /**
     * @test read
     */
    public function test_read_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->create();

        $dbCustomerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepo->find($customerMasterCategoryAssigned->id);

        $dbCustomerMasterCategoryAssigned = $dbCustomerMasterCategoryAssigned->toArray();
        $this->assertModelData($customerMasterCategoryAssigned->toArray(), $dbCustomerMasterCategoryAssigned);
    }

    /**
     * @test update
     */
    public function test_update_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->create();
        $fakeCustomerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->make()->toArray();

        $updatedCustomerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepo->update($fakeCustomerMasterCategoryAssigned, $customerMasterCategoryAssigned->id);

        $this->assertModelData($fakeCustomerMasterCategoryAssigned, $updatedCustomerMasterCategoryAssigned->toArray());
        $dbCustomerMasterCategoryAssigned = $this->customerMasterCategoryAssignedRepo->find($customerMasterCategoryAssigned->id);
        $this->assertModelData($fakeCustomerMasterCategoryAssigned, $dbCustomerMasterCategoryAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_master_category_assigned()
    {
        $customerMasterCategoryAssigned = factory(CustomerMasterCategoryAssigned::class)->create();

        $resp = $this->customerMasterCategoryAssignedRepo->delete($customerMasterCategoryAssigned->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerMasterCategoryAssigned::find($customerMasterCategoryAssigned->id), 'CustomerMasterCategoryAssigned should not exist in DB');
    }
}
