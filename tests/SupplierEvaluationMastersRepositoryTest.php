<?php namespace Tests\Repositories;

use App\Models\SupplierEvaluationMasters;
use App\Repositories\SupplierEvaluationMastersRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierEvaluationMastersRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierEvaluationMastersRepository
     */
    protected $supplierEvaluationMastersRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierEvaluationMastersRepo = \App::make(SupplierEvaluationMastersRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->make()->toArray();

        $createdSupplierEvaluationMasters = $this->supplierEvaluationMastersRepo->create($supplierEvaluationMasters);

        $createdSupplierEvaluationMasters = $createdSupplierEvaluationMasters->toArray();
        $this->assertArrayHasKey('id', $createdSupplierEvaluationMasters);
        $this->assertNotNull($createdSupplierEvaluationMasters['id'], 'Created SupplierEvaluationMasters must have id specified');
        $this->assertNotNull(SupplierEvaluationMasters::find($createdSupplierEvaluationMasters['id']), 'SupplierEvaluationMasters with given id must be in DB');
        $this->assertModelData($supplierEvaluationMasters, $createdSupplierEvaluationMasters);
    }

    /**
     * @test read
     */
    public function test_read_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->create();

        $dbSupplierEvaluationMasters = $this->supplierEvaluationMastersRepo->find($supplierEvaluationMasters->id);

        $dbSupplierEvaluationMasters = $dbSupplierEvaluationMasters->toArray();
        $this->assertModelData($supplierEvaluationMasters->toArray(), $dbSupplierEvaluationMasters);
    }

    /**
     * @test update
     */
    public function test_update_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->create();
        $fakeSupplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->make()->toArray();

        $updatedSupplierEvaluationMasters = $this->supplierEvaluationMastersRepo->update($fakeSupplierEvaluationMasters, $supplierEvaluationMasters->id);

        $this->assertModelData($fakeSupplierEvaluationMasters, $updatedSupplierEvaluationMasters->toArray());
        $dbSupplierEvaluationMasters = $this->supplierEvaluationMastersRepo->find($supplierEvaluationMasters->id);
        $this->assertModelData($fakeSupplierEvaluationMasters, $dbSupplierEvaluationMasters->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_evaluation_masters()
    {
        $supplierEvaluationMasters = factory(SupplierEvaluationMasters::class)->create();

        $resp = $this->supplierEvaluationMastersRepo->delete($supplierEvaluationMasters->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierEvaluationMasters::find($supplierEvaluationMasters->id), 'SupplierEvaluationMasters should not exist in DB');
    }
}
