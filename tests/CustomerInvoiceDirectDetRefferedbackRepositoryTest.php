<?php

use App\Models\CustomerInvoiceDirectDetRefferedback;
use App\Repositories\CustomerInvoiceDirectDetRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectDetRefferedbackRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceDirectDetRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceDirectDetRefferedbackRepository
     */
    protected $customerInvoiceDirectDetRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerInvoiceDirectDetRefferedbackRepo = App::make(CustomerInvoiceDirectDetRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->fakeCustomerInvoiceDirectDetRefferedbackData();
        $createdCustomerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepo->create($customerInvoiceDirectDetRefferedback);
        $createdCustomerInvoiceDirectDetRefferedback = $createdCustomerInvoiceDirectDetRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceDirectDetRefferedback);
        $this->assertNotNull($createdCustomerInvoiceDirectDetRefferedback['id'], 'Created CustomerInvoiceDirectDetRefferedback must have id specified');
        $this->assertNotNull(CustomerInvoiceDirectDetRefferedback::find($createdCustomerInvoiceDirectDetRefferedback['id']), 'CustomerInvoiceDirectDetRefferedback with given id must be in DB');
        $this->assertModelData($customerInvoiceDirectDetRefferedback, $createdCustomerInvoiceDirectDetRefferedback);
    }

    /**
     * @test read
     */
    public function testReadCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->makeCustomerInvoiceDirectDetRefferedback();
        $dbCustomerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepo->find($customerInvoiceDirectDetRefferedback->id);
        $dbCustomerInvoiceDirectDetRefferedback = $dbCustomerInvoiceDirectDetRefferedback->toArray();
        $this->assertModelData($customerInvoiceDirectDetRefferedback->toArray(), $dbCustomerInvoiceDirectDetRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->makeCustomerInvoiceDirectDetRefferedback();
        $fakeCustomerInvoiceDirectDetRefferedback = $this->fakeCustomerInvoiceDirectDetRefferedbackData();
        $updatedCustomerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepo->update($fakeCustomerInvoiceDirectDetRefferedback, $customerInvoiceDirectDetRefferedback->id);
        $this->assertModelData($fakeCustomerInvoiceDirectDetRefferedback, $updatedCustomerInvoiceDirectDetRefferedback->toArray());
        $dbCustomerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepo->find($customerInvoiceDirectDetRefferedback->id);
        $this->assertModelData($fakeCustomerInvoiceDirectDetRefferedback, $dbCustomerInvoiceDirectDetRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->makeCustomerInvoiceDirectDetRefferedback();
        $resp = $this->customerInvoiceDirectDetRefferedbackRepo->delete($customerInvoiceDirectDetRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceDirectDetRefferedback::find($customerInvoiceDirectDetRefferedback->id), 'CustomerInvoiceDirectDetRefferedback should not exist in DB');
    }
}
