<?php namespace Tests\Repositories;

use App\Models\SupplierCatalogMaster;
use App\Repositories\SupplierCatalogMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSupplierCatalogMasterTrait;
use Tests\ApiTestTrait;

class SupplierCatalogMasterRepositoryTest extends TestCase
{
    use MakeSupplierCatalogMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCatalogMasterRepository
     */
    protected $supplierCatalogMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierCatalogMasterRepo = \App::make(SupplierCatalogMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->fakeSupplierCatalogMasterData();
        $createdSupplierCatalogMaster = $this->supplierCatalogMasterRepo->create($supplierCatalogMaster);
        $createdSupplierCatalogMaster = $createdSupplierCatalogMaster->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCatalogMaster);
        $this->assertNotNull($createdSupplierCatalogMaster['id'], 'Created SupplierCatalogMaster must have id specified');
        $this->assertNotNull(SupplierCatalogMaster::find($createdSupplierCatalogMaster['id']), 'SupplierCatalogMaster with given id must be in DB');
        $this->assertModelData($supplierCatalogMaster, $createdSupplierCatalogMaster);
    }

    /**
     * @test read
     */
    public function test_read_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->makeSupplierCatalogMaster();
        $dbSupplierCatalogMaster = $this->supplierCatalogMasterRepo->find($supplierCatalogMaster->id);
        $dbSupplierCatalogMaster = $dbSupplierCatalogMaster->toArray();
        $this->assertModelData($supplierCatalogMaster->toArray(), $dbSupplierCatalogMaster);
    }

    /**
     * @test update
     */
    public function test_update_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->makeSupplierCatalogMaster();
        $fakeSupplierCatalogMaster = $this->fakeSupplierCatalogMasterData();
        $updatedSupplierCatalogMaster = $this->supplierCatalogMasterRepo->update($fakeSupplierCatalogMaster, $supplierCatalogMaster->id);
        $this->assertModelData($fakeSupplierCatalogMaster, $updatedSupplierCatalogMaster->toArray());
        $dbSupplierCatalogMaster = $this->supplierCatalogMasterRepo->find($supplierCatalogMaster->id);
        $this->assertModelData($fakeSupplierCatalogMaster, $dbSupplierCatalogMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_catalog_master()
    {
        $supplierCatalogMaster = $this->makeSupplierCatalogMaster();
        $resp = $this->supplierCatalogMasterRepo->delete($supplierCatalogMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCatalogMaster::find($supplierCatalogMaster->id), 'SupplierCatalogMaster should not exist in DB');
    }
}
