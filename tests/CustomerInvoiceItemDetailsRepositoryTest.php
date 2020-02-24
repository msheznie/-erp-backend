<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceItemDetails;
use App\Repositories\CustomerInvoiceItemDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerInvoiceItemDetailsTrait;
use Tests\ApiTestTrait;

class CustomerInvoiceItemDetailsRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceItemDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceItemDetailsRepository
     */
    protected $customerInvoiceItemDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceItemDetailsRepo = \App::make(CustomerInvoiceItemDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->fakeCustomerInvoiceItemDetailsData();
        $createdCustomerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepo->create($customerInvoiceItemDetails);
        $createdCustomerInvoiceItemDetails = $createdCustomerInvoiceItemDetails->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceItemDetails);
        $this->assertNotNull($createdCustomerInvoiceItemDetails['id'], 'Created CustomerInvoiceItemDetails must have id specified');
        $this->assertNotNull(CustomerInvoiceItemDetails::find($createdCustomerInvoiceItemDetails['id']), 'CustomerInvoiceItemDetails with given id must be in DB');
        $this->assertModelData($customerInvoiceItemDetails, $createdCustomerInvoiceItemDetails);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->makeCustomerInvoiceItemDetails();
        $dbCustomerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepo->find($customerInvoiceItemDetails->id);
        $dbCustomerInvoiceItemDetails = $dbCustomerInvoiceItemDetails->toArray();
        $this->assertModelData($customerInvoiceItemDetails->toArray(), $dbCustomerInvoiceItemDetails);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->makeCustomerInvoiceItemDetails();
        $fakeCustomerInvoiceItemDetails = $this->fakeCustomerInvoiceItemDetailsData();
        $updatedCustomerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepo->update($fakeCustomerInvoiceItemDetails, $customerInvoiceItemDetails->id);
        $this->assertModelData($fakeCustomerInvoiceItemDetails, $updatedCustomerInvoiceItemDetails->toArray());
        $dbCustomerInvoiceItemDetails = $this->customerInvoiceItemDetailsRepo->find($customerInvoiceItemDetails->id);
        $this->assertModelData($fakeCustomerInvoiceItemDetails, $dbCustomerInvoiceItemDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->makeCustomerInvoiceItemDetails();
        $resp = $this->customerInvoiceItemDetailsRepo->delete($customerInvoiceItemDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceItemDetails::find($customerInvoiceItemDetails->id), 'CustomerInvoiceItemDetails should not exist in DB');
    }
}
