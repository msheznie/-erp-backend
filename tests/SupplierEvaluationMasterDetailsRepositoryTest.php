<?php namespace Tests\Repositories;

use App\Models\SupplierEvaluationMasterDetails;
use App\Repositories\SupplierEvaluationMasterDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierEvaluationMasterDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierEvaluationMasterDetailsRepository
     */
    protected $supplierEvaluationMasterDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierEvaluationMasterDetailsRepo = \App::make(SupplierEvaluationMasterDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->make()->toArray();

        $createdSupplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepo->create($supplierEvaluationMasterDetails);

        $createdSupplierEvaluationMasterDetails = $createdSupplierEvaluationMasterDetails->toArray();
        $this->assertArrayHasKey('id', $createdSupplierEvaluationMasterDetails);
        $this->assertNotNull($createdSupplierEvaluationMasterDetails['id'], 'Created SupplierEvaluationMasterDetails must have id specified');
        $this->assertNotNull(SupplierEvaluationMasterDetails::find($createdSupplierEvaluationMasterDetails['id']), 'SupplierEvaluationMasterDetails with given id must be in DB');
        $this->assertModelData($supplierEvaluationMasterDetails, $createdSupplierEvaluationMasterDetails);
    }

    /**
     * @test read
     */
    public function test_read_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->create();

        $dbSupplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepo->find($supplierEvaluationMasterDetails->id);

        $dbSupplierEvaluationMasterDetails = $dbSupplierEvaluationMasterDetails->toArray();
        $this->assertModelData($supplierEvaluationMasterDetails->toArray(), $dbSupplierEvaluationMasterDetails);
    }

    /**
     * @test update
     */
    public function test_update_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->create();
        $fakeSupplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->make()->toArray();

        $updatedSupplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepo->update($fakeSupplierEvaluationMasterDetails, $supplierEvaluationMasterDetails->id);

        $this->assertModelData($fakeSupplierEvaluationMasterDetails, $updatedSupplierEvaluationMasterDetails->toArray());
        $dbSupplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepo->find($supplierEvaluationMasterDetails->id);
        $this->assertModelData($fakeSupplierEvaluationMasterDetails, $dbSupplierEvaluationMasterDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_evaluation_master_details()
    {
        $supplierEvaluationMasterDetails = factory(SupplierEvaluationMasterDetails::class)->create();

        $resp = $this->supplierEvaluationMasterDetailsRepo->delete($supplierEvaluationMasterDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierEvaluationMasterDetails::find($supplierEvaluationMasterDetails->id), 'SupplierEvaluationMasterDetails should not exist in DB');
    }
}
