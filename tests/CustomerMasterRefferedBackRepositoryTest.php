<?php

use App\Models\CustomerMasterRefferedBack;
use App\Repositories\CustomerMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerMasterRefferedBackRepositoryTest extends TestCase
{
    use MakeCustomerMasterRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerMasterRefferedBackRepository
     */
    protected $customerMasterRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerMasterRefferedBackRepo = App::make(CustomerMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->fakeCustomerMasterRefferedBackData();
        $createdCustomerMasterRefferedBack = $this->customerMasterRefferedBackRepo->create($customerMasterRefferedBack);
        $createdCustomerMasterRefferedBack = $createdCustomerMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdCustomerMasterRefferedBack);
        $this->assertNotNull($createdCustomerMasterRefferedBack['id'], 'Created CustomerMasterRefferedBack must have id specified');
        $this->assertNotNull(CustomerMasterRefferedBack::find($createdCustomerMasterRefferedBack['id']), 'CustomerMasterRefferedBack with given id must be in DB');
        $this->assertModelData($customerMasterRefferedBack, $createdCustomerMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->makeCustomerMasterRefferedBack();
        $dbCustomerMasterRefferedBack = $this->customerMasterRefferedBackRepo->find($customerMasterRefferedBack->id);
        $dbCustomerMasterRefferedBack = $dbCustomerMasterRefferedBack->toArray();
        $this->assertModelData($customerMasterRefferedBack->toArray(), $dbCustomerMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->makeCustomerMasterRefferedBack();
        $fakeCustomerMasterRefferedBack = $this->fakeCustomerMasterRefferedBackData();
        $updatedCustomerMasterRefferedBack = $this->customerMasterRefferedBackRepo->update($fakeCustomerMasterRefferedBack, $customerMasterRefferedBack->id);
        $this->assertModelData($fakeCustomerMasterRefferedBack, $updatedCustomerMasterRefferedBack->toArray());
        $dbCustomerMasterRefferedBack = $this->customerMasterRefferedBackRepo->find($customerMasterRefferedBack->id);
        $this->assertModelData($fakeCustomerMasterRefferedBack, $dbCustomerMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerMasterRefferedBack()
    {
        $customerMasterRefferedBack = $this->makeCustomerMasterRefferedBack();
        $resp = $this->customerMasterRefferedBackRepo->delete($customerMasterRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerMasterRefferedBack::find($customerMasterRefferedBack->id), 'CustomerMasterRefferedBack should not exist in DB');
    }
}
