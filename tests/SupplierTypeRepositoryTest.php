<?php

use App\Models\SupplierType;
use App\Repositories\SupplierTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierTypeRepositoryTest extends TestCase
{
    use MakeSupplierTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierTypeRepository
     */
    protected $supplierTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierTypeRepo = App::make(SupplierTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierType()
    {
        $supplierType = $this->fakeSupplierTypeData();
        $createdSupplierType = $this->supplierTypeRepo->create($supplierType);
        $createdSupplierType = $createdSupplierType->toArray();
        $this->assertArrayHasKey('id', $createdSupplierType);
        $this->assertNotNull($createdSupplierType['id'], 'Created SupplierType must have id specified');
        $this->assertNotNull(SupplierType::find($createdSupplierType['id']), 'SupplierType with given id must be in DB');
        $this->assertModelData($supplierType, $createdSupplierType);
    }

    /**
     * @test read
     */
    public function testReadSupplierType()
    {
        $supplierType = $this->makeSupplierType();
        $dbSupplierType = $this->supplierTypeRepo->find($supplierType->id);
        $dbSupplierType = $dbSupplierType->toArray();
        $this->assertModelData($supplierType->toArray(), $dbSupplierType);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierType()
    {
        $supplierType = $this->makeSupplierType();
        $fakeSupplierType = $this->fakeSupplierTypeData();
        $updatedSupplierType = $this->supplierTypeRepo->update($fakeSupplierType, $supplierType->id);
        $this->assertModelData($fakeSupplierType, $updatedSupplierType->toArray());
        $dbSupplierType = $this->supplierTypeRepo->find($supplierType->id);
        $this->assertModelData($fakeSupplierType, $dbSupplierType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierType()
    {
        $supplierType = $this->makeSupplierType();
        $resp = $this->supplierTypeRepo->delete($supplierType->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierType::find($supplierType->id), 'SupplierType should not exist in DB');
    }
}
