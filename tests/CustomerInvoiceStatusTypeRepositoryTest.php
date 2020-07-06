<?php namespace Tests\Repositories;

use App\Models\CustomerInvoiceStatusType;
use App\Repositories\CustomerInvoiceStatusTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomerInvoiceStatusTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceStatusTypeRepository
     */
    protected $customerInvoiceStatusTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerInvoiceStatusTypeRepo = \App::make(CustomerInvoiceStatusTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->make()->toArray();

        $createdCustomerInvoiceStatusType = $this->customerInvoiceStatusTypeRepo->create($customerInvoiceStatusType);

        $createdCustomerInvoiceStatusType = $createdCustomerInvoiceStatusType->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceStatusType);
        $this->assertNotNull($createdCustomerInvoiceStatusType['id'], 'Created CustomerInvoiceStatusType must have id specified');
        $this->assertNotNull(CustomerInvoiceStatusType::find($createdCustomerInvoiceStatusType['id']), 'CustomerInvoiceStatusType with given id must be in DB');
        $this->assertModelData($customerInvoiceStatusType, $createdCustomerInvoiceStatusType);
    }

    /**
     * @test read
     */
    public function test_read_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->create();

        $dbCustomerInvoiceStatusType = $this->customerInvoiceStatusTypeRepo->find($customerInvoiceStatusType->id);

        $dbCustomerInvoiceStatusType = $dbCustomerInvoiceStatusType->toArray();
        $this->assertModelData($customerInvoiceStatusType->toArray(), $dbCustomerInvoiceStatusType);
    }

    /**
     * @test update
     */
    public function test_update_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->create();
        $fakeCustomerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->make()->toArray();

        $updatedCustomerInvoiceStatusType = $this->customerInvoiceStatusTypeRepo->update($fakeCustomerInvoiceStatusType, $customerInvoiceStatusType->id);

        $this->assertModelData($fakeCustomerInvoiceStatusType, $updatedCustomerInvoiceStatusType->toArray());
        $dbCustomerInvoiceStatusType = $this->customerInvoiceStatusTypeRepo->find($customerInvoiceStatusType->id);
        $this->assertModelData($fakeCustomerInvoiceStatusType, $dbCustomerInvoiceStatusType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->create();

        $resp = $this->customerInvoiceStatusTypeRepo->delete($customerInvoiceStatusType->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceStatusType::find($customerInvoiceStatusType->id), 'CustomerInvoiceStatusType should not exist in DB');
    }
}
