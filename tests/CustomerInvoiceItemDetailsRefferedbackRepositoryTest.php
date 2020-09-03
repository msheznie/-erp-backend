<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceItemDetailsRefferedback;
use App\Repositories\CustomerInvoiceItemDetailsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerInvoiceItemDetailsRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceItemDetailsRefferedbackRepository
     */
    protected $customerInvoiceItemDetailsRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceItemDetailsRefferedbackRepo = \App::make(CustomerInvoiceItemDetailsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->make()->toArray();

        $createdCustomerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepo->create($customerInvoiceItemDetailsRefferedback);

        $createdCustomerInvoiceItemDetailsRefferedback = $createdCustomerInvoiceItemDetailsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceItemDetailsRefferedback);
        $this->assertNotNull($createdCustomerInvoiceItemDetailsRefferedback['id'], 'Created CustomerInvoiceItemDetailsRefferedback must have id specified');
        $this->assertNotNull(CustomerInvoiceItemDetailsRefferedback::find($createdCustomerInvoiceItemDetailsRefferedback['id']), 'CustomerInvoiceItemDetailsRefferedback with given id must be in DB');
        $this->assertModelData($customerInvoiceItemDetailsRefferedback, $createdCustomerInvoiceItemDetailsRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->create();

        $dbCustomerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepo->find($customerInvoiceItemDetailsRefferedback->id);

        $dbCustomerInvoiceItemDetailsRefferedback = $dbCustomerInvoiceItemDetailsRefferedback->toArray();
        $this->assertModelData($customerInvoiceItemDetailsRefferedback->toArray(), $dbCustomerInvoiceItemDetailsRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->create();
        $fakeCustomerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->make()->toArray();

        $updatedCustomerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepo->update($fakeCustomerInvoiceItemDetailsRefferedback, $customerInvoiceItemDetailsRefferedback->id);

        $this->assertModelData($fakeCustomerInvoiceItemDetailsRefferedback, $updatedCustomerInvoiceItemDetailsRefferedback->toArray());
        $dbCustomerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepo->find($customerInvoiceItemDetailsRefferedback->id);
        $this->assertModelData($fakeCustomerInvoiceItemDetailsRefferedback, $dbCustomerInvoiceItemDetailsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->create();

        $resp = $this->customerInvoiceItemDetailsRefferedbackRepo->delete($customerInvoiceItemDetailsRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceItemDetailsRefferedback::find($customerInvoiceItemDetailsRefferedback->id), 'CustomerInvoiceItemDetailsRefferedback should not exist in DB');
    }
}
