<?php namespace Tests\Repositories;

use App\Models\SupplierBusinessCategoryAssign;
use App\Repositories\SupplierBusinessCategoryAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierBusinessCategoryAssignRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierBusinessCategoryAssignRepository
     */
    protected $supplierBusinessCategoryAssignRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierBusinessCategoryAssignRepo = \App::make(SupplierBusinessCategoryAssignRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->make()->toArray();

        $createdSupplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepo->create($supplierBusinessCategoryAssign);

        $createdSupplierBusinessCategoryAssign = $createdSupplierBusinessCategoryAssign->toArray();
        $this->assertArrayHasKey('id', $createdSupplierBusinessCategoryAssign);
        $this->assertNotNull($createdSupplierBusinessCategoryAssign['id'], 'Created SupplierBusinessCategoryAssign must have id specified');
        $this->assertNotNull(SupplierBusinessCategoryAssign::find($createdSupplierBusinessCategoryAssign['id']), 'SupplierBusinessCategoryAssign with given id must be in DB');
        $this->assertModelData($supplierBusinessCategoryAssign, $createdSupplierBusinessCategoryAssign);
    }

    /**
     * @test read
     */
    public function test_read_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->create();

        $dbSupplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepo->find($supplierBusinessCategoryAssign->id);

        $dbSupplierBusinessCategoryAssign = $dbSupplierBusinessCategoryAssign->toArray();
        $this->assertModelData($supplierBusinessCategoryAssign->toArray(), $dbSupplierBusinessCategoryAssign);
    }

    /**
     * @test update
     */
    public function test_update_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->create();
        $fakeSupplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->make()->toArray();

        $updatedSupplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepo->update($fakeSupplierBusinessCategoryAssign, $supplierBusinessCategoryAssign->id);

        $this->assertModelData($fakeSupplierBusinessCategoryAssign, $updatedSupplierBusinessCategoryAssign->toArray());
        $dbSupplierBusinessCategoryAssign = $this->supplierBusinessCategoryAssignRepo->find($supplierBusinessCategoryAssign->id);
        $this->assertModelData($fakeSupplierBusinessCategoryAssign, $dbSupplierBusinessCategoryAssign->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_business_category_assign()
    {
        $supplierBusinessCategoryAssign = factory(SupplierBusinessCategoryAssign::class)->create();

        $resp = $this->supplierBusinessCategoryAssignRepo->delete($supplierBusinessCategoryAssign->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierBusinessCategoryAssign::find($supplierBusinessCategoryAssign->id), 'SupplierBusinessCategoryAssign should not exist in DB');
    }
}
