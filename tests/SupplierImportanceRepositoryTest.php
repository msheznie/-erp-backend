<?php

use App\Models\SupplierImportance;
use App\Repositories\SupplierImportanceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierImportanceRepositoryTest extends TestCase
{
    use MakeSupplierImportanceTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierImportanceRepository
     */
    protected $supplierImportanceRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierImportanceRepo = App::make(SupplierImportanceRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierImportance()
    {
        $supplierImportance = $this->fakeSupplierImportanceData();
        $createdSupplierImportance = $this->supplierImportanceRepo->create($supplierImportance);
        $createdSupplierImportance = $createdSupplierImportance->toArray();
        $this->assertArrayHasKey('id', $createdSupplierImportance);
        $this->assertNotNull($createdSupplierImportance['id'], 'Created SupplierImportance must have id specified');
        $this->assertNotNull(SupplierImportance::find($createdSupplierImportance['id']), 'SupplierImportance with given id must be in DB');
        $this->assertModelData($supplierImportance, $createdSupplierImportance);
    }

    /**
     * @test read
     */
    public function testReadSupplierImportance()
    {
        $supplierImportance = $this->makeSupplierImportance();
        $dbSupplierImportance = $this->supplierImportanceRepo->find($supplierImportance->id);
        $dbSupplierImportance = $dbSupplierImportance->toArray();
        $this->assertModelData($supplierImportance->toArray(), $dbSupplierImportance);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierImportance()
    {
        $supplierImportance = $this->makeSupplierImportance();
        $fakeSupplierImportance = $this->fakeSupplierImportanceData();
        $updatedSupplierImportance = $this->supplierImportanceRepo->update($fakeSupplierImportance, $supplierImportance->id);
        $this->assertModelData($fakeSupplierImportance, $updatedSupplierImportance->toArray());
        $dbSupplierImportance = $this->supplierImportanceRepo->find($supplierImportance->id);
        $this->assertModelData($fakeSupplierImportance, $dbSupplierImportance->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierImportance()
    {
        $supplierImportance = $this->makeSupplierImportance();
        $resp = $this->supplierImportanceRepo->delete($supplierImportance->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierImportance::find($supplierImportance->id), 'SupplierImportance should not exist in DB');
    }
}
