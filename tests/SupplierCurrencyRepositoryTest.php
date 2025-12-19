<?php

use App\Models\SupplierCurrency;
use App\Repositories\SupplierCurrencyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCurrencyRepositoryTest extends TestCase
{
    use MakeSupplierCurrencyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCurrencyRepository
     */
    protected $supplierCurrencyRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierCurrencyRepo = App::make(SupplierCurrencyRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierCurrency()
    {
        $supplierCurrency = $this->fakeSupplierCurrencyData();
        $createdSupplierCurrency = $this->supplierCurrencyRepo->create($supplierCurrency);
        $createdSupplierCurrency = $createdSupplierCurrency->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCurrency);
        $this->assertNotNull($createdSupplierCurrency['id'], 'Created SupplierCurrency must have id specified');
        $this->assertNotNull(SupplierCurrency::find($createdSupplierCurrency['id']), 'SupplierCurrency with given id must be in DB');
        $this->assertModelData($supplierCurrency, $createdSupplierCurrency);
    }

    /**
     * @test read
     */
    public function testReadSupplierCurrency()
    {
        $supplierCurrency = $this->makeSupplierCurrency();
        $dbSupplierCurrency = $this->supplierCurrencyRepo->find($supplierCurrency->id);
        $dbSupplierCurrency = $dbSupplierCurrency->toArray();
        $this->assertModelData($supplierCurrency->toArray(), $dbSupplierCurrency);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierCurrency()
    {
        $supplierCurrency = $this->makeSupplierCurrency();
        $fakeSupplierCurrency = $this->fakeSupplierCurrencyData();
        $updatedSupplierCurrency = $this->supplierCurrencyRepo->update($fakeSupplierCurrency, $supplierCurrency->id);
        $this->assertModelData($fakeSupplierCurrency, $updatedSupplierCurrency->toArray());
        $dbSupplierCurrency = $this->supplierCurrencyRepo->find($supplierCurrency->id);
        $this->assertModelData($fakeSupplierCurrency, $dbSupplierCurrency->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierCurrency()
    {
        $supplierCurrency = $this->makeSupplierCurrency();
        $resp = $this->supplierCurrencyRepo->delete($supplierCurrency->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCurrency::find($supplierCurrency->id), 'SupplierCurrency should not exist in DB');
    }
}
