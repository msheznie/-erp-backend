<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceTracking;
use App\Repositories\CustomerInvoiceTrackingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerInvoiceTrackingTrait;
use Tests\ApiTestTrait;

class CustomerInvoiceTrackingRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceTrackingTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceTrackingRepository
     */
    protected $customerInvoiceTrackingRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceTrackingRepo = \App::make(CustomerInvoiceTrackingRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->fakeCustomerInvoiceTrackingData();
        $createdCustomerInvoiceTracking = $this->customerInvoiceTrackingRepo->create($customerInvoiceTracking);
        $createdCustomerInvoiceTracking = $createdCustomerInvoiceTracking->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceTracking);
        $this->assertNotNull($createdCustomerInvoiceTracking['id'], 'Created CustomerInvoiceTracking must have id specified');
        $this->assertNotNull(CustomerInvoiceTracking::find($createdCustomerInvoiceTracking['id']), 'CustomerInvoiceTracking with given id must be in DB');
        $this->assertModelData($customerInvoiceTracking, $createdCustomerInvoiceTracking);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->makeCustomerInvoiceTracking();
        $dbCustomerInvoiceTracking = $this->customerInvoiceTrackingRepo->find($customerInvoiceTracking->id);
        $dbCustomerInvoiceTracking = $dbCustomerInvoiceTracking->toArray();
        $this->assertModelData($customerInvoiceTracking->toArray(), $dbCustomerInvoiceTracking);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->makeCustomerInvoiceTracking();
        $fakeCustomerInvoiceTracking = $this->fakeCustomerInvoiceTrackingData();
        $updatedCustomerInvoiceTracking = $this->customerInvoiceTrackingRepo->update($fakeCustomerInvoiceTracking, $customerInvoiceTracking->id);
        $this->assertModelData($fakeCustomerInvoiceTracking, $updatedCustomerInvoiceTracking->toArray());
        $dbCustomerInvoiceTracking = $this->customerInvoiceTrackingRepo->find($customerInvoiceTracking->id);
        $this->assertModelData($fakeCustomerInvoiceTracking, $dbCustomerInvoiceTracking->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->makeCustomerInvoiceTracking();
        $resp = $this->customerInvoiceTrackingRepo->delete($customerInvoiceTracking->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceTracking::find($customerInvoiceTracking->id), 'CustomerInvoiceTracking should not exist in DB');
    }
}
