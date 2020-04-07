<?php namespace Tests\Repositories;

use App\Models\SupplierCatalogDetail;
use App\Repositories\SupplierCatalogDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSupplierCatalogDetailTrait;
use Tests\ApiTestTrait;

class SupplierCatalogDetailRepositoryTest extends TestCase
{
    use MakeSupplierCatalogDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCatalogDetailRepository
     */
    protected $supplierCatalogDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierCatalogDetailRepo = \App::make(SupplierCatalogDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->fakeSupplierCatalogDetailData();
        $createdSupplierCatalogDetail = $this->supplierCatalogDetailRepo->create($supplierCatalogDetail);
        $createdSupplierCatalogDetail = $createdSupplierCatalogDetail->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCatalogDetail);
        $this->assertNotNull($createdSupplierCatalogDetail['id'], 'Created SupplierCatalogDetail must have id specified');
        $this->assertNotNull(SupplierCatalogDetail::find($createdSupplierCatalogDetail['id']), 'SupplierCatalogDetail with given id must be in DB');
        $this->assertModelData($supplierCatalogDetail, $createdSupplierCatalogDetail);
    }

    /**
     * @test read
     */
    public function test_read_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->makeSupplierCatalogDetail();
        $dbSupplierCatalogDetail = $this->supplierCatalogDetailRepo->find($supplierCatalogDetail->id);
        $dbSupplierCatalogDetail = $dbSupplierCatalogDetail->toArray();
        $this->assertModelData($supplierCatalogDetail->toArray(), $dbSupplierCatalogDetail);
    }

    /**
     * @test update
     */
    public function test_update_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->makeSupplierCatalogDetail();
        $fakeSupplierCatalogDetail = $this->fakeSupplierCatalogDetailData();
        $updatedSupplierCatalogDetail = $this->supplierCatalogDetailRepo->update($fakeSupplierCatalogDetail, $supplierCatalogDetail->id);
        $this->assertModelData($fakeSupplierCatalogDetail, $updatedSupplierCatalogDetail->toArray());
        $dbSupplierCatalogDetail = $this->supplierCatalogDetailRepo->find($supplierCatalogDetail->id);
        $this->assertModelData($fakeSupplierCatalogDetail, $dbSupplierCatalogDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_catalog_detail()
    {
        $supplierCatalogDetail = $this->makeSupplierCatalogDetail();
        $resp = $this->supplierCatalogDetailRepo->delete($supplierCatalogDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCatalogDetail::find($supplierCatalogDetail->id), 'SupplierCatalogDetail should not exist in DB');
    }
}
