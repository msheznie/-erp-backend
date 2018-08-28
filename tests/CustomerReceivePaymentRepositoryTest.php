<?php

use App\Models\CustomerReceivePayment;
use App\Repositories\CustomerReceivePaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerReceivePaymentRepositoryTest extends TestCase
{
    use MakeCustomerReceivePaymentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerReceivePaymentRepository
     */
    protected $customerReceivePaymentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerReceivePaymentRepo = App::make(CustomerReceivePaymentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerReceivePayment()
    {
        $customerReceivePayment = $this->fakeCustomerReceivePaymentData();
        $createdCustomerReceivePayment = $this->customerReceivePaymentRepo->create($customerReceivePayment);
        $createdCustomerReceivePayment = $createdCustomerReceivePayment->toArray();
        $this->assertArrayHasKey('id', $createdCustomerReceivePayment);
        $this->assertNotNull($createdCustomerReceivePayment['id'], 'Created CustomerReceivePayment must have id specified');
        $this->assertNotNull(CustomerReceivePayment::find($createdCustomerReceivePayment['id']), 'CustomerReceivePayment with given id must be in DB');
        $this->assertModelData($customerReceivePayment, $createdCustomerReceivePayment);
    }

    /**
     * @test read
     */
    public function testReadCustomerReceivePayment()
    {
        $customerReceivePayment = $this->makeCustomerReceivePayment();
        $dbCustomerReceivePayment = $this->customerReceivePaymentRepo->find($customerReceivePayment->id);
        $dbCustomerReceivePayment = $dbCustomerReceivePayment->toArray();
        $this->assertModelData($customerReceivePayment->toArray(), $dbCustomerReceivePayment);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerReceivePayment()
    {
        $customerReceivePayment = $this->makeCustomerReceivePayment();
        $fakeCustomerReceivePayment = $this->fakeCustomerReceivePaymentData();
        $updatedCustomerReceivePayment = $this->customerReceivePaymentRepo->update($fakeCustomerReceivePayment, $customerReceivePayment->id);
        $this->assertModelData($fakeCustomerReceivePayment, $updatedCustomerReceivePayment->toArray());
        $dbCustomerReceivePayment = $this->customerReceivePaymentRepo->find($customerReceivePayment->id);
        $this->assertModelData($fakeCustomerReceivePayment, $dbCustomerReceivePayment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerReceivePayment()
    {
        $customerReceivePayment = $this->makeCustomerReceivePayment();
        $resp = $this->customerReceivePaymentRepo->delete($customerReceivePayment->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerReceivePayment::find($customerReceivePayment->id), 'CustomerReceivePayment should not exist in DB');
    }
}
