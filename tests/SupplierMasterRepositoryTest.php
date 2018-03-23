<?php

use App\Models\SupplierMaster;
use App\Repositories\SupplierMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierMasterRepositoryTest extends TestCase
{
    use MakeSupplierMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierMasterRepository
     */
    protected $supplierMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierMasterRepo = App::make(SupplierMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierMaster()
    {
        $supplierMaster = $this->fakeSupplierMasterData();
        $createdSupplierMaster = $this->supplierMasterRepo->create($supplierMaster);
        $createdSupplierMaster = $createdSupplierMaster->toArray();
        $this->assertArrayHasKey('id', $createdSupplierMaster);
        $this->assertNotNull($createdSupplierMaster['id'], 'Created SupplierMaster must have id specified');
        $this->assertNotNull(SupplierMaster::find($createdSupplierMaster['id']), 'SupplierMaster with given id must be in DB');
        $this->assertModelData($supplierMaster, $createdSupplierMaster);
    }

    /**
     * @test read
     */
    public function testReadSupplierMaster()
    {
        $supplierMaster = $this->makeSupplierMaster();
        $dbSupplierMaster = $this->supplierMasterRepo->find($supplierMaster->id);
        $dbSupplierMaster = $dbSupplierMaster->toArray();
        $this->assertModelData($supplierMaster->toArray(), $dbSupplierMaster);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierMaster()
    {
        $supplierMaster = $this->makeSupplierMaster();
        $fakeSupplierMaster = $this->fakeSupplierMasterData();
        $updatedSupplierMaster = $this->supplierMasterRepo->update($fakeSupplierMaster, $supplierMaster->id);
        $this->assertModelData($fakeSupplierMaster, $updatedSupplierMaster->toArray());
        $dbSupplierMaster = $this->supplierMasterRepo->find($supplierMaster->id);
        $this->assertModelData($fakeSupplierMaster, $dbSupplierMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierMaster()
    {
        $supplierMaster = $this->makeSupplierMaster();
        $resp = $this->supplierMasterRepo->delete($supplierMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierMaster::find($supplierMaster->id), 'SupplierMaster should not exist in DB');
    }
}
