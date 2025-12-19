<?php

use App\Models\CustomerAssigned;
use App\Repositories\CustomerAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerAssignedRepositoryTest extends TestCase
{
    use MakeCustomerAssignedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerAssignedRepository
     */
    protected $customerAssignedRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerAssignedRepo = App::make(CustomerAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerAssigned()
    {
        $customerAssigned = $this->fakeCustomerAssignedData();
        $createdCustomerAssigned = $this->customerAssignedRepo->create($customerAssigned);
        $createdCustomerAssigned = $createdCustomerAssigned->toArray();
        $this->assertArrayHasKey('id', $createdCustomerAssigned);
        $this->assertNotNull($createdCustomerAssigned['id'], 'Created CustomerAssigned must have id specified');
        $this->assertNotNull(CustomerAssigned::find($createdCustomerAssigned['id']), 'CustomerAssigned with given id must be in DB');
        $this->assertModelData($customerAssigned, $createdCustomerAssigned);
    }

    /**
     * @test read
     */
    public function testReadCustomerAssigned()
    {
        $customerAssigned = $this->makeCustomerAssigned();
        $dbCustomerAssigned = $this->customerAssignedRepo->find($customerAssigned->id);
        $dbCustomerAssigned = $dbCustomerAssigned->toArray();
        $this->assertModelData($customerAssigned->toArray(), $dbCustomerAssigned);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerAssigned()
    {
        $customerAssigned = $this->makeCustomerAssigned();
        $fakeCustomerAssigned = $this->fakeCustomerAssignedData();
        $updatedCustomerAssigned = $this->customerAssignedRepo->update($fakeCustomerAssigned, $customerAssigned->id);
        $this->assertModelData($fakeCustomerAssigned, $updatedCustomerAssigned->toArray());
        $dbCustomerAssigned = $this->customerAssignedRepo->find($customerAssigned->id);
        $this->assertModelData($fakeCustomerAssigned, $dbCustomerAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerAssigned()
    {
        $customerAssigned = $this->makeCustomerAssigned();
        $resp = $this->customerAssignedRepo->delete($customerAssigned->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerAssigned::find($customerAssigned->id), 'CustomerAssigned should not exist in DB');
    }
}
