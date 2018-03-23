<?php

use App\Models\SupplierSubCategoryAssign;
use App\Repositories\SupplierSubCategoryAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierSubCategoryAssignRepositoryTest extends TestCase
{
    use MakeSupplierSubCategoryAssignTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierSubCategoryAssignRepository
     */
    protected $supplierSubCategoryAssignRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierSubCategoryAssignRepo = App::make(SupplierSubCategoryAssignRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->fakeSupplierSubCategoryAssignData();
        $createdSupplierSubCategoryAssign = $this->supplierSubCategoryAssignRepo->create($supplierSubCategoryAssign);
        $createdSupplierSubCategoryAssign = $createdSupplierSubCategoryAssign->toArray();
        $this->assertArrayHasKey('id', $createdSupplierSubCategoryAssign);
        $this->assertNotNull($createdSupplierSubCategoryAssign['id'], 'Created SupplierSubCategoryAssign must have id specified');
        $this->assertNotNull(SupplierSubCategoryAssign::find($createdSupplierSubCategoryAssign['id']), 'SupplierSubCategoryAssign with given id must be in DB');
        $this->assertModelData($supplierSubCategoryAssign, $createdSupplierSubCategoryAssign);
    }

    /**
     * @test read
     */
    public function testReadSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->makeSupplierSubCategoryAssign();
        $dbSupplierSubCategoryAssign = $this->supplierSubCategoryAssignRepo->find($supplierSubCategoryAssign->id);
        $dbSupplierSubCategoryAssign = $dbSupplierSubCategoryAssign->toArray();
        $this->assertModelData($supplierSubCategoryAssign->toArray(), $dbSupplierSubCategoryAssign);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->makeSupplierSubCategoryAssign();
        $fakeSupplierSubCategoryAssign = $this->fakeSupplierSubCategoryAssignData();
        $updatedSupplierSubCategoryAssign = $this->supplierSubCategoryAssignRepo->update($fakeSupplierSubCategoryAssign, $supplierSubCategoryAssign->id);
        $this->assertModelData($fakeSupplierSubCategoryAssign, $updatedSupplierSubCategoryAssign->toArray());
        $dbSupplierSubCategoryAssign = $this->supplierSubCategoryAssignRepo->find($supplierSubCategoryAssign->id);
        $this->assertModelData($fakeSupplierSubCategoryAssign, $dbSupplierSubCategoryAssign->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierSubCategoryAssign()
    {
        $supplierSubCategoryAssign = $this->makeSupplierSubCategoryAssign();
        $resp = $this->supplierSubCategoryAssignRepo->delete($supplierSubCategoryAssign->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierSubCategoryAssign::find($supplierSubCategoryAssign->id), 'SupplierSubCategoryAssign should not exist in DB');
    }
}
