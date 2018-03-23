<?php

use App\Models\SupplierAssigned;
use App\Repositories\SupplierAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierAssignedRepositoryTest extends TestCase
{
    use MakeSupplierAssignedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierAssignedRepository
     */
    protected $supplierAssignedRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierAssignedRepo = App::make(SupplierAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierAssigned()
    {
        $supplierAssigned = $this->fakeSupplierAssignedData();
        $createdSupplierAssigned = $this->supplierAssignedRepo->create($supplierAssigned);
        $createdSupplierAssigned = $createdSupplierAssigned->toArray();
        $this->assertArrayHasKey('id', $createdSupplierAssigned);
        $this->assertNotNull($createdSupplierAssigned['id'], 'Created SupplierAssigned must have id specified');
        $this->assertNotNull(SupplierAssigned::find($createdSupplierAssigned['id']), 'SupplierAssigned with given id must be in DB');
        $this->assertModelData($supplierAssigned, $createdSupplierAssigned);
    }

    /**
     * @test read
     */
    public function testReadSupplierAssigned()
    {
        $supplierAssigned = $this->makeSupplierAssigned();
        $dbSupplierAssigned = $this->supplierAssignedRepo->find($supplierAssigned->id);
        $dbSupplierAssigned = $dbSupplierAssigned->toArray();
        $this->assertModelData($supplierAssigned->toArray(), $dbSupplierAssigned);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierAssigned()
    {
        $supplierAssigned = $this->makeSupplierAssigned();
        $fakeSupplierAssigned = $this->fakeSupplierAssignedData();
        $updatedSupplierAssigned = $this->supplierAssignedRepo->update($fakeSupplierAssigned, $supplierAssigned->id);
        $this->assertModelData($fakeSupplierAssigned, $updatedSupplierAssigned->toArray());
        $dbSupplierAssigned = $this->supplierAssignedRepo->find($supplierAssigned->id);
        $this->assertModelData($fakeSupplierAssigned, $dbSupplierAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierAssigned()
    {
        $supplierAssigned = $this->makeSupplierAssigned();
        $resp = $this->supplierAssignedRepo->delete($supplierAssigned->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierAssigned::find($supplierAssigned->id), 'SupplierAssigned should not exist in DB');
    }
}
