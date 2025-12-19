<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceLogistic;
use App\Repositories\CustomerInvoiceLogisticRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerInvoiceLogisticRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceLogisticRepository
     */
    protected $customerInvoiceLogisticRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceLogisticRepo = \App::make(CustomerInvoiceLogisticRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->make()->toArray();

        $createdCustomerInvoiceLogistic = $this->customerInvoiceLogisticRepo->create($customerInvoiceLogistic);

        $createdCustomerInvoiceLogistic = $createdCustomerInvoiceLogistic->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceLogistic);
        $this->assertNotNull($createdCustomerInvoiceLogistic['id'], 'Created CustomerInvoiceLogistic must have id specified');
        $this->assertNotNull(CustomerInvoiceLogistic::find($createdCustomerInvoiceLogistic['id']), 'CustomerInvoiceLogistic with given id must be in DB');
        $this->assertModelData($customerInvoiceLogistic, $createdCustomerInvoiceLogistic);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->create();

        $dbCustomerInvoiceLogistic = $this->customerInvoiceLogisticRepo->find($customerInvoiceLogistic->id);

        $dbCustomerInvoiceLogistic = $dbCustomerInvoiceLogistic->toArray();
        $this->assertModelData($customerInvoiceLogistic->toArray(), $dbCustomerInvoiceLogistic);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->create();
        $fakeCustomerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->make()->toArray();

        $updatedCustomerInvoiceLogistic = $this->customerInvoiceLogisticRepo->update($fakeCustomerInvoiceLogistic, $customerInvoiceLogistic->id);

        $this->assertModelData($fakeCustomerInvoiceLogistic, $updatedCustomerInvoiceLogistic->toArray());
        $dbCustomerInvoiceLogistic = $this->customerInvoiceLogisticRepo->find($customerInvoiceLogistic->id);
        $this->assertModelData($fakeCustomerInvoiceLogistic, $dbCustomerInvoiceLogistic->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->create();

        $resp = $this->customerInvoiceLogisticRepo->delete($customerInvoiceLogistic->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceLogistic::find($customerInvoiceLogistic->id), 'CustomerInvoiceLogistic should not exist in DB');
    }
}
