<?php

use App\Models\CustomerMaster;
use App\Repositories\CustomerMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerMasterRepositoryTest extends TestCase
{
    use MakeCustomerMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerMasterRepository
     */
    protected $customerMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerMasterRepo = App::make(CustomerMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerMaster()
    {
        $customerMaster = $this->fakeCustomerMasterData();
        $createdCustomerMaster = $this->customerMasterRepo->create($customerMaster);
        $createdCustomerMaster = $createdCustomerMaster->toArray();
        $this->assertArrayHasKey('id', $createdCustomerMaster);
        $this->assertNotNull($createdCustomerMaster['id'], 'Created CustomerMaster must have id specified');
        $this->assertNotNull(CustomerMaster::find($createdCustomerMaster['id']), 'CustomerMaster with given id must be in DB');
        $this->assertModelData($customerMaster, $createdCustomerMaster);
    }

    /**
     * @test read
     */
    public function testReadCustomerMaster()
    {
        $customerMaster = $this->makeCustomerMaster();
        $dbCustomerMaster = $this->customerMasterRepo->find($customerMaster->id);
        $dbCustomerMaster = $dbCustomerMaster->toArray();
        $this->assertModelData($customerMaster->toArray(), $dbCustomerMaster);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerMaster()
    {
        $customerMaster = $this->makeCustomerMaster();
        $fakeCustomerMaster = $this->fakeCustomerMasterData();
        $updatedCustomerMaster = $this->customerMasterRepo->update($fakeCustomerMaster, $customerMaster->id);
        $this->assertModelData($fakeCustomerMaster, $updatedCustomerMaster->toArray());
        $dbCustomerMaster = $this->customerMasterRepo->find($customerMaster->id);
        $this->assertModelData($fakeCustomerMaster, $dbCustomerMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerMaster()
    {
        $customerMaster = $this->makeCustomerMaster();
        $resp = $this->customerMasterRepo->delete($customerMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerMaster::find($customerMaster->id), 'CustomerMaster should not exist in DB');
    }
}
