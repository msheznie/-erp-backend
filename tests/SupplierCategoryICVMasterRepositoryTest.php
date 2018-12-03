<?php

use App\Models\SupplierCategoryICVMaster;
use App\Repositories\SupplierCategoryICVMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategoryICVMasterRepositoryTest extends TestCase
{
    use MakeSupplierCategoryICVMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCategoryICVMasterRepository
     */
    protected $supplierCategoryICVMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierCategoryICVMasterRepo = App::make(SupplierCategoryICVMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->fakeSupplierCategoryICVMasterData();
        $createdSupplierCategoryICVMaster = $this->supplierCategoryICVMasterRepo->create($supplierCategoryICVMaster);
        $createdSupplierCategoryICVMaster = $createdSupplierCategoryICVMaster->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCategoryICVMaster);
        $this->assertNotNull($createdSupplierCategoryICVMaster['id'], 'Created SupplierCategoryICVMaster must have id specified');
        $this->assertNotNull(SupplierCategoryICVMaster::find($createdSupplierCategoryICVMaster['id']), 'SupplierCategoryICVMaster with given id must be in DB');
        $this->assertModelData($supplierCategoryICVMaster, $createdSupplierCategoryICVMaster);
    }

    /**
     * @test read
     */
    public function testReadSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->makeSupplierCategoryICVMaster();
        $dbSupplierCategoryICVMaster = $this->supplierCategoryICVMasterRepo->find($supplierCategoryICVMaster->id);
        $dbSupplierCategoryICVMaster = $dbSupplierCategoryICVMaster->toArray();
        $this->assertModelData($supplierCategoryICVMaster->toArray(), $dbSupplierCategoryICVMaster);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->makeSupplierCategoryICVMaster();
        $fakeSupplierCategoryICVMaster = $this->fakeSupplierCategoryICVMasterData();
        $updatedSupplierCategoryICVMaster = $this->supplierCategoryICVMasterRepo->update($fakeSupplierCategoryICVMaster, $supplierCategoryICVMaster->id);
        $this->assertModelData($fakeSupplierCategoryICVMaster, $updatedSupplierCategoryICVMaster->toArray());
        $dbSupplierCategoryICVMaster = $this->supplierCategoryICVMasterRepo->find($supplierCategoryICVMaster->id);
        $this->assertModelData($fakeSupplierCategoryICVMaster, $dbSupplierCategoryICVMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierCategoryICVMaster()
    {
        $supplierCategoryICVMaster = $this->makeSupplierCategoryICVMaster();
        $resp = $this->supplierCategoryICVMasterRepo->delete($supplierCategoryICVMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCategoryICVMaster::find($supplierCategoryICVMaster->id), 'SupplierCategoryICVMaster should not exist in DB');
    }
}
