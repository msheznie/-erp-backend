<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceUploadDetail;
use App\Repositories\CustomerInvoiceUploadDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerInvoiceUploadDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceUploadDetailRepository
     */
    protected $customerInvoiceUploadDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceUploadDetailRepo = \App::make(CustomerInvoiceUploadDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->make()->toArray();

        $createdCustomerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepo->create($customerInvoiceUploadDetail);

        $createdCustomerInvoiceUploadDetail = $createdCustomerInvoiceUploadDetail->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceUploadDetail);
        $this->assertNotNull($createdCustomerInvoiceUploadDetail['id'], 'Created CustomerInvoiceUploadDetail must have id specified');
        $this->assertNotNull(CustomerInvoiceUploadDetail::find($createdCustomerInvoiceUploadDetail['id']), 'CustomerInvoiceUploadDetail with given id must be in DB');
        $this->assertModelData($customerInvoiceUploadDetail, $createdCustomerInvoiceUploadDetail);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->create();

        $dbCustomerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepo->find($customerInvoiceUploadDetail->id);

        $dbCustomerInvoiceUploadDetail = $dbCustomerInvoiceUploadDetail->toArray();
        $this->assertModelData($customerInvoiceUploadDetail->toArray(), $dbCustomerInvoiceUploadDetail);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->create();
        $fakeCustomerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->make()->toArray();

        $updatedCustomerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepo->update($fakeCustomerInvoiceUploadDetail, $customerInvoiceUploadDetail->id);

        $this->assertModelData($fakeCustomerInvoiceUploadDetail, $updatedCustomerInvoiceUploadDetail->toArray());
        $dbCustomerInvoiceUploadDetail = $this->customerInvoiceUploadDetailRepo->find($customerInvoiceUploadDetail->id);
        $this->assertModelData($fakeCustomerInvoiceUploadDetail, $dbCustomerInvoiceUploadDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->create();

        $resp = $this->customerInvoiceUploadDetailRepo->delete($customerInvoiceUploadDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceUploadDetail::find($customerInvoiceUploadDetail->id), 'CustomerInvoiceUploadDetail should not exist in DB');
    }
}
