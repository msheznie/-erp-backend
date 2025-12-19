<?php

use App\Models\SupplierCategoryMaster;
use App\Repositories\SupplierCategoryMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategoryMasterRepositoryTest extends TestCase
{
    use MakeSupplierCategoryMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCategoryMasterRepository
     */
    protected $supplierCategoryMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierCategoryMasterRepo = App::make(SupplierCategoryMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->fakeSupplierCategoryMasterData();
        $createdSupplierCategoryMaster = $this->supplierCategoryMasterRepo->create($supplierCategoryMaster);
        $createdSupplierCategoryMaster = $createdSupplierCategoryMaster->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCategoryMaster);
        $this->assertNotNull($createdSupplierCategoryMaster['id'], 'Created SupplierCategoryMaster must have id specified');
        $this->assertNotNull(SupplierCategoryMaster::find($createdSupplierCategoryMaster['id']), 'SupplierCategoryMaster with given id must be in DB');
        $this->assertModelData($supplierCategoryMaster, $createdSupplierCategoryMaster);
    }

    /**
     * @test read
     */
    public function testReadSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->makeSupplierCategoryMaster();
        $dbSupplierCategoryMaster = $this->supplierCategoryMasterRepo->find($supplierCategoryMaster->id);
        $dbSupplierCategoryMaster = $dbSupplierCategoryMaster->toArray();
        $this->assertModelData($supplierCategoryMaster->toArray(), $dbSupplierCategoryMaster);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->makeSupplierCategoryMaster();
        $fakeSupplierCategoryMaster = $this->fakeSupplierCategoryMasterData();
        $updatedSupplierCategoryMaster = $this->supplierCategoryMasterRepo->update($fakeSupplierCategoryMaster, $supplierCategoryMaster->id);
        $this->assertModelData($fakeSupplierCategoryMaster, $updatedSupplierCategoryMaster->toArray());
        $dbSupplierCategoryMaster = $this->supplierCategoryMasterRepo->find($supplierCategoryMaster->id);
        $this->assertModelData($fakeSupplierCategoryMaster, $dbSupplierCategoryMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierCategoryMaster()
    {
        $supplierCategoryMaster = $this->makeSupplierCategoryMaster();
        $resp = $this->supplierCategoryMasterRepo->delete($supplierCategoryMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCategoryMaster::find($supplierCategoryMaster->id), 'SupplierCategoryMaster should not exist in DB');
    }
}
