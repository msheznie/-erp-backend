<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceTrackingDetail;
use App\Repositories\CustomerInvoiceTrackingDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerInvoiceTrackingDetailTrait;
use Tests\ApiTestTrait;

class CustomerInvoiceTrackingDetailRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceTrackingDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceTrackingDetailRepository
     */
    protected $customerInvoiceTrackingDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceTrackingDetailRepo = \App::make(CustomerInvoiceTrackingDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->fakeCustomerInvoiceTrackingDetailData();
        $createdCustomerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepo->create($customerInvoiceTrackingDetail);
        $createdCustomerInvoiceTrackingDetail = $createdCustomerInvoiceTrackingDetail->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceTrackingDetail);
        $this->assertNotNull($createdCustomerInvoiceTrackingDetail['id'], 'Created CustomerInvoiceTrackingDetail must have id specified');
        $this->assertNotNull(CustomerInvoiceTrackingDetail::find($createdCustomerInvoiceTrackingDetail['id']), 'CustomerInvoiceTrackingDetail with given id must be in DB');
        $this->assertModelData($customerInvoiceTrackingDetail, $createdCustomerInvoiceTrackingDetail);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->makeCustomerInvoiceTrackingDetail();
        $dbCustomerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepo->find($customerInvoiceTrackingDetail->id);
        $dbCustomerInvoiceTrackingDetail = $dbCustomerInvoiceTrackingDetail->toArray();
        $this->assertModelData($customerInvoiceTrackingDetail->toArray(), $dbCustomerInvoiceTrackingDetail);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->makeCustomerInvoiceTrackingDetail();
        $fakeCustomerInvoiceTrackingDetail = $this->fakeCustomerInvoiceTrackingDetailData();
        $updatedCustomerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepo->update($fakeCustomerInvoiceTrackingDetail, $customerInvoiceTrackingDetail->id);
        $this->assertModelData($fakeCustomerInvoiceTrackingDetail, $updatedCustomerInvoiceTrackingDetail->toArray());
        $dbCustomerInvoiceTrackingDetail = $this->customerInvoiceTrackingDetailRepo->find($customerInvoiceTrackingDetail->id);
        $this->assertModelData($fakeCustomerInvoiceTrackingDetail, $dbCustomerInvoiceTrackingDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->makeCustomerInvoiceTrackingDetail();
        $resp = $this->customerInvoiceTrackingDetailRepo->delete($customerInvoiceTrackingDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceTrackingDetail::find($customerInvoiceTrackingDetail->id), 'CustomerInvoiceTrackingDetail should not exist in DB');
    }
}
