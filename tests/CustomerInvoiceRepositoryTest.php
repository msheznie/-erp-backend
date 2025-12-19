<?php

use App\Models\CustomerInvoice;
use App\Repositories\CustomerInvoiceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceRepository
     */
    protected $customerInvoiceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerInvoiceRepo = App::make(CustomerInvoiceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerInvoice()
    {
        $customerInvoice = $this->fakeCustomerInvoiceData();
        $createdCustomerInvoice = $this->customerInvoiceRepo->create($customerInvoice);
        $createdCustomerInvoice = $createdCustomerInvoice->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoice);
        $this->assertNotNull($createdCustomerInvoice['id'], 'Created CustomerInvoice must have id specified');
        $this->assertNotNull(CustomerInvoice::find($createdCustomerInvoice['id']), 'CustomerInvoice with given id must be in DB');
        $this->assertModelData($customerInvoice, $createdCustomerInvoice);
    }

    /**
     * @test read
     */
    public function testReadCustomerInvoice()
    {
        $customerInvoice = $this->makeCustomerInvoice();
        $dbCustomerInvoice = $this->customerInvoiceRepo->find($customerInvoice->id);
        $dbCustomerInvoice = $dbCustomerInvoice->toArray();
        $this->assertModelData($customerInvoice->toArray(), $dbCustomerInvoice);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerInvoice()
    {
        $customerInvoice = $this->makeCustomerInvoice();
        $fakeCustomerInvoice = $this->fakeCustomerInvoiceData();
        $updatedCustomerInvoice = $this->customerInvoiceRepo->update($fakeCustomerInvoice, $customerInvoice->id);
        $this->assertModelData($fakeCustomerInvoice, $updatedCustomerInvoice->toArray());
        $dbCustomerInvoice = $this->customerInvoiceRepo->find($customerInvoice->id);
        $this->assertModelData($fakeCustomerInvoice, $dbCustomerInvoice->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerInvoice()
    {
        $customerInvoice = $this->makeCustomerInvoice();
        $resp = $this->customerInvoiceRepo->delete($customerInvoice->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoice::find($customerInvoice->id), 'CustomerInvoice should not exist in DB');
    }
}
