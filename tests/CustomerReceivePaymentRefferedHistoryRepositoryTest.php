<?php

use App\Models\CustomerReceivePaymentRefferedHistory;
use App\Repositories\CustomerReceivePaymentRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerReceivePaymentRefferedHistoryRepositoryTest extends TestCase
{
    use MakeCustomerReceivePaymentRefferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerReceivePaymentRefferedHistoryRepository
     */
    protected $customerReceivePaymentRefferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerReceivePaymentRefferedHistoryRepo = App::make(CustomerReceivePaymentRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->fakeCustomerReceivePaymentRefferedHistoryData();
        $createdCustomerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepo->create($customerReceivePaymentRefferedHistory);
        $createdCustomerReceivePaymentRefferedHistory = $createdCustomerReceivePaymentRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdCustomerReceivePaymentRefferedHistory);
        $this->assertNotNull($createdCustomerReceivePaymentRefferedHistory['id'], 'Created CustomerReceivePaymentRefferedHistory must have id specified');
        $this->assertNotNull(CustomerReceivePaymentRefferedHistory::find($createdCustomerReceivePaymentRefferedHistory['id']), 'CustomerReceivePaymentRefferedHistory with given id must be in DB');
        $this->assertModelData($customerReceivePaymentRefferedHistory, $createdCustomerReceivePaymentRefferedHistory);
    }

    /**
     * @test read
     */
    public function testReadCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->makeCustomerReceivePaymentRefferedHistory();
        $dbCustomerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepo->find($customerReceivePaymentRefferedHistory->id);
        $dbCustomerReceivePaymentRefferedHistory = $dbCustomerReceivePaymentRefferedHistory->toArray();
        $this->assertModelData($customerReceivePaymentRefferedHistory->toArray(), $dbCustomerReceivePaymentRefferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->makeCustomerReceivePaymentRefferedHistory();
        $fakeCustomerReceivePaymentRefferedHistory = $this->fakeCustomerReceivePaymentRefferedHistoryData();
        $updatedCustomerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepo->update($fakeCustomerReceivePaymentRefferedHistory, $customerReceivePaymentRefferedHistory->id);
        $this->assertModelData($fakeCustomerReceivePaymentRefferedHistory, $updatedCustomerReceivePaymentRefferedHistory->toArray());
        $dbCustomerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepo->find($customerReceivePaymentRefferedHistory->id);
        $this->assertModelData($fakeCustomerReceivePaymentRefferedHistory, $dbCustomerReceivePaymentRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerReceivePaymentRefferedHistory()
    {
        $customerReceivePaymentRefferedHistory = $this->makeCustomerReceivePaymentRefferedHistory();
        $resp = $this->customerReceivePaymentRefferedHistoryRepo->delete($customerReceivePaymentRefferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerReceivePaymentRefferedHistory::find($customerReceivePaymentRefferedHistory->id), 'CustomerReceivePaymentRefferedHistory should not exist in DB');
    }
}
