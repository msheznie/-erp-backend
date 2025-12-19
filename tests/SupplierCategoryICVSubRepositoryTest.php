<?php

use App\Models\SupplierCategoryICVSub;
use App\Repositories\SupplierCategoryICVSubRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCategoryICVSubRepositoryTest extends TestCase
{
    use MakeSupplierCategoryICVSubTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCategoryICVSubRepository
     */
    protected $supplierCategoryICVSubRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierCategoryICVSubRepo = App::make(SupplierCategoryICVSubRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->fakeSupplierCategoryICVSubData();
        $createdSupplierCategoryICVSub = $this->supplierCategoryICVSubRepo->create($supplierCategoryICVSub);
        $createdSupplierCategoryICVSub = $createdSupplierCategoryICVSub->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCategoryICVSub);
        $this->assertNotNull($createdSupplierCategoryICVSub['id'], 'Created SupplierCategoryICVSub must have id specified');
        $this->assertNotNull(SupplierCategoryICVSub::find($createdSupplierCategoryICVSub['id']), 'SupplierCategoryICVSub with given id must be in DB');
        $this->assertModelData($supplierCategoryICVSub, $createdSupplierCategoryICVSub);
    }

    /**
     * @test read
     */
    public function testReadSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->makeSupplierCategoryICVSub();
        $dbSupplierCategoryICVSub = $this->supplierCategoryICVSubRepo->find($supplierCategoryICVSub->id);
        $dbSupplierCategoryICVSub = $dbSupplierCategoryICVSub->toArray();
        $this->assertModelData($supplierCategoryICVSub->toArray(), $dbSupplierCategoryICVSub);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->makeSupplierCategoryICVSub();
        $fakeSupplierCategoryICVSub = $this->fakeSupplierCategoryICVSubData();
        $updatedSupplierCategoryICVSub = $this->supplierCategoryICVSubRepo->update($fakeSupplierCategoryICVSub, $supplierCategoryICVSub->id);
        $this->assertModelData($fakeSupplierCategoryICVSub, $updatedSupplierCategoryICVSub->toArray());
        $dbSupplierCategoryICVSub = $this->supplierCategoryICVSubRepo->find($supplierCategoryICVSub->id);
        $this->assertModelData($fakeSupplierCategoryICVSub, $dbSupplierCategoryICVSub->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierCategoryICVSub()
    {
        $supplierCategoryICVSub = $this->makeSupplierCategoryICVSub();
        $resp = $this->supplierCategoryICVSubRepo->delete($supplierCategoryICVSub->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCategoryICVSub::find($supplierCategoryICVSub->id), 'SupplierCategoryICVSub should not exist in DB');
    }
}
