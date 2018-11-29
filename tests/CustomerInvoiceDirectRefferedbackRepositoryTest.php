<?php

use App\Models\CustomerInvoiceDirectRefferedback;
use App\Repositories\CustomerInvoiceDirectRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectRefferedbackRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceDirectRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceDirectRefferedbackRepository
     */
    protected $customerInvoiceDirectRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerInvoiceDirectRefferedbackRepo = App::make(CustomerInvoiceDirectRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->fakeCustomerInvoiceDirectRefferedbackData();
        $createdCustomerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepo->create($customerInvoiceDirectRefferedback);
        $createdCustomerInvoiceDirectRefferedback = $createdCustomerInvoiceDirectRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceDirectRefferedback);
        $this->assertNotNull($createdCustomerInvoiceDirectRefferedback['id'], 'Created CustomerInvoiceDirectRefferedback must have id specified');
        $this->assertNotNull(CustomerInvoiceDirectRefferedback::find($createdCustomerInvoiceDirectRefferedback['id']), 'CustomerInvoiceDirectRefferedback with given id must be in DB');
        $this->assertModelData($customerInvoiceDirectRefferedback, $createdCustomerInvoiceDirectRefferedback);
    }

    /**
     * @test read
     */
    public function testReadCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->makeCustomerInvoiceDirectRefferedback();
        $dbCustomerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepo->find($customerInvoiceDirectRefferedback->id);
        $dbCustomerInvoiceDirectRefferedback = $dbCustomerInvoiceDirectRefferedback->toArray();
        $this->assertModelData($customerInvoiceDirectRefferedback->toArray(), $dbCustomerInvoiceDirectRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->makeCustomerInvoiceDirectRefferedback();
        $fakeCustomerInvoiceDirectRefferedback = $this->fakeCustomerInvoiceDirectRefferedbackData();
        $updatedCustomerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepo->update($fakeCustomerInvoiceDirectRefferedback, $customerInvoiceDirectRefferedback->id);
        $this->assertModelData($fakeCustomerInvoiceDirectRefferedback, $updatedCustomerInvoiceDirectRefferedback->toArray());
        $dbCustomerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepo->find($customerInvoiceDirectRefferedback->id);
        $this->assertModelData($fakeCustomerInvoiceDirectRefferedback, $dbCustomerInvoiceDirectRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->makeCustomerInvoiceDirectRefferedback();
        $resp = $this->customerInvoiceDirectRefferedbackRepo->delete($customerInvoiceDirectRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceDirectRefferedback::find($customerInvoiceDirectRefferedback->id), 'CustomerInvoiceDirectRefferedback should not exist in DB');
    }
}
