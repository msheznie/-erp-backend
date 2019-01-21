<?php

use App\Models\CustomerMasterCategory;
use App\Repositories\CustomerMasterCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerMasterCategoryRepositoryTest extends TestCase
{
    use MakeCustomerMasterCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerMasterCategoryRepository
     */
    protected $customerMasterCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerMasterCategoryRepo = App::make(CustomerMasterCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerMasterCategory()
    {
        $customerMasterCategory = $this->fakeCustomerMasterCategoryData();
        $createdCustomerMasterCategory = $this->customerMasterCategoryRepo->create($customerMasterCategory);
        $createdCustomerMasterCategory = $createdCustomerMasterCategory->toArray();
        $this->assertArrayHasKey('id', $createdCustomerMasterCategory);
        $this->assertNotNull($createdCustomerMasterCategory['id'], 'Created CustomerMasterCategory must have id specified');
        $this->assertNotNull(CustomerMasterCategory::find($createdCustomerMasterCategory['id']), 'CustomerMasterCategory with given id must be in DB');
        $this->assertModelData($customerMasterCategory, $createdCustomerMasterCategory);
    }

    /**
     * @test read
     */
    public function testReadCustomerMasterCategory()
    {
        $customerMasterCategory = $this->makeCustomerMasterCategory();
        $dbCustomerMasterCategory = $this->customerMasterCategoryRepo->find($customerMasterCategory->id);
        $dbCustomerMasterCategory = $dbCustomerMasterCategory->toArray();
        $this->assertModelData($customerMasterCategory->toArray(), $dbCustomerMasterCategory);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerMasterCategory()
    {
        $customerMasterCategory = $this->makeCustomerMasterCategory();
        $fakeCustomerMasterCategory = $this->fakeCustomerMasterCategoryData();
        $updatedCustomerMasterCategory = $this->customerMasterCategoryRepo->update($fakeCustomerMasterCategory, $customerMasterCategory->id);
        $this->assertModelData($fakeCustomerMasterCategory, $updatedCustomerMasterCategory->toArray());
        $dbCustomerMasterCategory = $this->customerMasterCategoryRepo->find($customerMasterCategory->id);
        $this->assertModelData($fakeCustomerMasterCategory, $dbCustomerMasterCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerMasterCategory()
    {
        $customerMasterCategory = $this->makeCustomerMasterCategory();
        $resp = $this->customerMasterCategoryRepo->delete($customerMasterCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerMasterCategory::find($customerMasterCategory->id), 'CustomerMasterCategory should not exist in DB');
    }
}
