<?php

use App\Models\SupplierMasterRefferedBack;
use App\Repositories\SupplierMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierMasterRefferedBackRepositoryTest extends TestCase
{
    use MakeSupplierMasterRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierMasterRefferedBackRepository
     */
    protected $supplierMasterRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierMasterRefferedBackRepo = App::make(SupplierMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->fakeSupplierMasterRefferedBackData();
        $createdSupplierMasterRefferedBack = $this->supplierMasterRefferedBackRepo->create($supplierMasterRefferedBack);
        $createdSupplierMasterRefferedBack = $createdSupplierMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdSupplierMasterRefferedBack);
        $this->assertNotNull($createdSupplierMasterRefferedBack['id'], 'Created SupplierMasterRefferedBack must have id specified');
        $this->assertNotNull(SupplierMasterRefferedBack::find($createdSupplierMasterRefferedBack['id']), 'SupplierMasterRefferedBack with given id must be in DB');
        $this->assertModelData($supplierMasterRefferedBack, $createdSupplierMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->makeSupplierMasterRefferedBack();
        $dbSupplierMasterRefferedBack = $this->supplierMasterRefferedBackRepo->find($supplierMasterRefferedBack->id);
        $dbSupplierMasterRefferedBack = $dbSupplierMasterRefferedBack->toArray();
        $this->assertModelData($supplierMasterRefferedBack->toArray(), $dbSupplierMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->makeSupplierMasterRefferedBack();
        $fakeSupplierMasterRefferedBack = $this->fakeSupplierMasterRefferedBackData();
        $updatedSupplierMasterRefferedBack = $this->supplierMasterRefferedBackRepo->update($fakeSupplierMasterRefferedBack, $supplierMasterRefferedBack->id);
        $this->assertModelData($fakeSupplierMasterRefferedBack, $updatedSupplierMasterRefferedBack->toArray());
        $dbSupplierMasterRefferedBack = $this->supplierMasterRefferedBackRepo->find($supplierMasterRefferedBack->id);
        $this->assertModelData($fakeSupplierMasterRefferedBack, $dbSupplierMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierMasterRefferedBack()
    {
        $supplierMasterRefferedBack = $this->makeSupplierMasterRefferedBack();
        $resp = $this->supplierMasterRefferedBackRepo->delete($supplierMasterRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierMasterRefferedBack::find($supplierMasterRefferedBack->id), 'SupplierMasterRefferedBack should not exist in DB');
    }
}
