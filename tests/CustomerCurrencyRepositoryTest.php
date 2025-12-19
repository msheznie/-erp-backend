<?php

use App\Models\CustomerCurrency;
use App\Repositories\CustomerCurrencyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerCurrencyRepositoryTest extends TestCase
{
    use MakeCustomerCurrencyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerCurrencyRepository
     */
    protected $customerCurrencyRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerCurrencyRepo = App::make(CustomerCurrencyRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerCurrency()
    {
        $customerCurrency = $this->fakeCustomerCurrencyData();
        $createdCustomerCurrency = $this->customerCurrencyRepo->create($customerCurrency);
        $createdCustomerCurrency = $createdCustomerCurrency->toArray();
        $this->assertArrayHasKey('id', $createdCustomerCurrency);
        $this->assertNotNull($createdCustomerCurrency['id'], 'Created CustomerCurrency must have id specified');
        $this->assertNotNull(CustomerCurrency::find($createdCustomerCurrency['id']), 'CustomerCurrency with given id must be in DB');
        $this->assertModelData($customerCurrency, $createdCustomerCurrency);
    }

    /**
     * @test read
     */
    public function testReadCustomerCurrency()
    {
        $customerCurrency = $this->makeCustomerCurrency();
        $dbCustomerCurrency = $this->customerCurrencyRepo->find($customerCurrency->id);
        $dbCustomerCurrency = $dbCustomerCurrency->toArray();
        $this->assertModelData($customerCurrency->toArray(), $dbCustomerCurrency);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerCurrency()
    {
        $customerCurrency = $this->makeCustomerCurrency();
        $fakeCustomerCurrency = $this->fakeCustomerCurrencyData();
        $updatedCustomerCurrency = $this->customerCurrencyRepo->update($fakeCustomerCurrency, $customerCurrency->id);
        $this->assertModelData($fakeCustomerCurrency, $updatedCustomerCurrency->toArray());
        $dbCustomerCurrency = $this->customerCurrencyRepo->find($customerCurrency->id);
        $this->assertModelData($fakeCustomerCurrency, $dbCustomerCurrency->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerCurrency()
    {
        $customerCurrency = $this->makeCustomerCurrency();
        $resp = $this->customerCurrencyRepo->delete($customerCurrency->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerCurrency::find($customerCurrency->id), 'CustomerCurrency should not exist in DB');
    }
}
