<?php

use App\Models\SupplierCategorySub;
use App\Repositories\SupplierCategorySubRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategorySubRepositoryTest extends TestCase
{
    use MakeSupplierCategorySubTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCategorySubRepository
     */
    protected $supplierCategorySubRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierCategorySubRepo = App::make(SupplierCategorySubRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierCategorySub()
    {
        $supplierCategorySub = $this->fakeSupplierCategorySubData();
        $createdSupplierCategorySub = $this->supplierCategorySubRepo->create($supplierCategorySub);
        $createdSupplierCategorySub = $createdSupplierCategorySub->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCategorySub);
        $this->assertNotNull($createdSupplierCategorySub['id'], 'Created SupplierCategorySub must have id specified');
        $this->assertNotNull(SupplierCategorySub::find($createdSupplierCategorySub['id']), 'SupplierCategorySub with given id must be in DB');
        $this->assertModelData($supplierCategorySub, $createdSupplierCategorySub);
    }

    /**
     * @test read
     */
    public function testReadSupplierCategorySub()
    {
        $supplierCategorySub = $this->makeSupplierCategorySub();
        $dbSupplierCategorySub = $this->supplierCategorySubRepo->find($supplierCategorySub->id);
        $dbSupplierCategorySub = $dbSupplierCategorySub->toArray();
        $this->assertModelData($supplierCategorySub->toArray(), $dbSupplierCategorySub);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierCategorySub()
    {
        $supplierCategorySub = $this->makeSupplierCategorySub();
        $fakeSupplierCategorySub = $this->fakeSupplierCategorySubData();
        $updatedSupplierCategorySub = $this->supplierCategorySubRepo->update($fakeSupplierCategorySub, $supplierCategorySub->id);
        $this->assertModelData($fakeSupplierCategorySub, $updatedSupplierCategorySub->toArray());
        $dbSupplierCategorySub = $this->supplierCategorySubRepo->find($supplierCategorySub->id);
        $this->assertModelData($fakeSupplierCategorySub, $dbSupplierCategorySub->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierCategorySub()
    {
        $supplierCategorySub = $this->makeSupplierCategorySub();
        $resp = $this->supplierCategorySubRepo->delete($supplierCategorySub->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCategorySub::find($supplierCategorySub->id), 'SupplierCategorySub should not exist in DB');
    }
}
