<?php

use App\Models\CustomerInvoiceDirect;
use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceDirectTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceDirectRepository
     */
    protected $customerInvoiceDirectRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerInvoiceDirectRepo = App::make(CustomerInvoiceDirectRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->fakeCustomerInvoiceDirectData();
        $createdCustomerInvoiceDirect = $this->customerInvoiceDirectRepo->create($customerInvoiceDirect);
        $createdCustomerInvoiceDirect = $createdCustomerInvoiceDirect->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceDirect);
        $this->assertNotNull($createdCustomerInvoiceDirect['id'], 'Created CustomerInvoiceDirect must have id specified');
        $this->assertNotNull(CustomerInvoiceDirect::find($createdCustomerInvoiceDirect['id']), 'CustomerInvoiceDirect with given id must be in DB');
        $this->assertModelData($customerInvoiceDirect, $createdCustomerInvoiceDirect);
    }

    /**
     * @test read
     */
    public function testReadCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->makeCustomerInvoiceDirect();
        $dbCustomerInvoiceDirect = $this->customerInvoiceDirectRepo->find($customerInvoiceDirect->id);
        $dbCustomerInvoiceDirect = $dbCustomerInvoiceDirect->toArray();
        $this->assertModelData($customerInvoiceDirect->toArray(), $dbCustomerInvoiceDirect);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->makeCustomerInvoiceDirect();
        $fakeCustomerInvoiceDirect = $this->fakeCustomerInvoiceDirectData();
        $updatedCustomerInvoiceDirect = $this->customerInvoiceDirectRepo->update($fakeCustomerInvoiceDirect, $customerInvoiceDirect->id);
        $this->assertModelData($fakeCustomerInvoiceDirect, $updatedCustomerInvoiceDirect->toArray());
        $dbCustomerInvoiceDirect = $this->customerInvoiceDirectRepo->find($customerInvoiceDirect->id);
        $this->assertModelData($fakeCustomerInvoiceDirect, $dbCustomerInvoiceDirect->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->makeCustomerInvoiceDirect();
        $resp = $this->customerInvoiceDirectRepo->delete($customerInvoiceDirect->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceDirect::find($customerInvoiceDirect->id), 'CustomerInvoiceDirect should not exist in DB');
    }
}
