<?php namespace Tests\Repositories;

use App\Models\RegisteredSupplierCurrency;
use App\Repositories\RegisteredSupplierCurrencyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisteredSupplierCurrencyRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisteredSupplierCurrencyRepository
     */
    protected $registeredSupplierCurrencyRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registeredSupplierCurrencyRepo = \App::make(RegisteredSupplierCurrencyRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->make()->toArray();

        $createdRegisteredSupplierCurrency = $this->registeredSupplierCurrencyRepo->create($registeredSupplierCurrency);

        $createdRegisteredSupplierCurrency = $createdRegisteredSupplierCurrency->toArray();
        $this->assertArrayHasKey('id', $createdRegisteredSupplierCurrency);
        $this->assertNotNull($createdRegisteredSupplierCurrency['id'], 'Created RegisteredSupplierCurrency must have id specified');
        $this->assertNotNull(RegisteredSupplierCurrency::find($createdRegisteredSupplierCurrency['id']), 'RegisteredSupplierCurrency with given id must be in DB');
        $this->assertModelData($registeredSupplierCurrency, $createdRegisteredSupplierCurrency);
    }

    /**
     * @test read
     */
    public function test_read_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->create();

        $dbRegisteredSupplierCurrency = $this->registeredSupplierCurrencyRepo->find($registeredSupplierCurrency->id);

        $dbRegisteredSupplierCurrency = $dbRegisteredSupplierCurrency->toArray();
        $this->assertModelData($registeredSupplierCurrency->toArray(), $dbRegisteredSupplierCurrency);
    }

    /**
     * @test update
     */
    public function test_update_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->create();
        $fakeRegisteredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->make()->toArray();

        $updatedRegisteredSupplierCurrency = $this->registeredSupplierCurrencyRepo->update($fakeRegisteredSupplierCurrency, $registeredSupplierCurrency->id);

        $this->assertModelData($fakeRegisteredSupplierCurrency, $updatedRegisteredSupplierCurrency->toArray());
        $dbRegisteredSupplierCurrency = $this->registeredSupplierCurrencyRepo->find($registeredSupplierCurrency->id);
        $this->assertModelData($fakeRegisteredSupplierCurrency, $dbRegisteredSupplierCurrency->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_registered_supplier_currency()
    {
        $registeredSupplierCurrency = factory(RegisteredSupplierCurrency::class)->create();

        $resp = $this->registeredSupplierCurrencyRepo->delete($registeredSupplierCurrency->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisteredSupplierCurrency::find($registeredSupplierCurrency->id), 'RegisteredSupplierCurrency should not exist in DB');
    }
}
