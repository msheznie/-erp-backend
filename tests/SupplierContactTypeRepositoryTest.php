<?php

use App\Models\SupplierContactType;
use App\Repositories\SupplierContactTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierContactTypeRepositoryTest extends TestCase
{
    use MakeSupplierContactTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierContactTypeRepository
     */
    protected $supplierContactTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierContactTypeRepo = App::make(SupplierContactTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierContactType()
    {
        $supplierContactType = $this->fakeSupplierContactTypeData();
        $createdSupplierContactType = $this->supplierContactTypeRepo->create($supplierContactType);
        $createdSupplierContactType = $createdSupplierContactType->toArray();
        $this->assertArrayHasKey('id', $createdSupplierContactType);
        $this->assertNotNull($createdSupplierContactType['id'], 'Created SupplierContactType must have id specified');
        $this->assertNotNull(SupplierContactType::find($createdSupplierContactType['id']), 'SupplierContactType with given id must be in DB');
        $this->assertModelData($supplierContactType, $createdSupplierContactType);
    }

    /**
     * @test read
     */
    public function testReadSupplierContactType()
    {
        $supplierContactType = $this->makeSupplierContactType();
        $dbSupplierContactType = $this->supplierContactTypeRepo->find($supplierContactType->id);
        $dbSupplierContactType = $dbSupplierContactType->toArray();
        $this->assertModelData($supplierContactType->toArray(), $dbSupplierContactType);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierContactType()
    {
        $supplierContactType = $this->makeSupplierContactType();
        $fakeSupplierContactType = $this->fakeSupplierContactTypeData();
        $updatedSupplierContactType = $this->supplierContactTypeRepo->update($fakeSupplierContactType, $supplierContactType->id);
        $this->assertModelData($fakeSupplierContactType, $updatedSupplierContactType->toArray());
        $dbSupplierContactType = $this->supplierContactTypeRepo->find($supplierContactType->id);
        $this->assertModelData($fakeSupplierContactType, $dbSupplierContactType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierContactType()
    {
        $supplierContactType = $this->makeSupplierContactType();
        $resp = $this->supplierContactTypeRepo->delete($supplierContactType->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierContactType::find($supplierContactType->id), 'SupplierContactType should not exist in DB');
    }
}
