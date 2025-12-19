<?php

use App\Models\SupplierCritical;
use App\Repositories\SupplierCriticalRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCriticalRepositoryTest extends TestCase
{
    use MakeSupplierCriticalTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierCriticalRepository
     */
    protected $supplierCriticalRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierCriticalRepo = App::make(SupplierCriticalRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierCritical()
    {
        $supplierCritical = $this->fakeSupplierCriticalData();
        $createdSupplierCritical = $this->supplierCriticalRepo->create($supplierCritical);
        $createdSupplierCritical = $createdSupplierCritical->toArray();
        $this->assertArrayHasKey('id', $createdSupplierCritical);
        $this->assertNotNull($createdSupplierCritical['id'], 'Created SupplierCritical must have id specified');
        $this->assertNotNull(SupplierCritical::find($createdSupplierCritical['id']), 'SupplierCritical with given id must be in DB');
        $this->assertModelData($supplierCritical, $createdSupplierCritical);
    }

    /**
     * @test read
     */
    public function testReadSupplierCritical()
    {
        $supplierCritical = $this->makeSupplierCritical();
        $dbSupplierCritical = $this->supplierCriticalRepo->find($supplierCritical->id);
        $dbSupplierCritical = $dbSupplierCritical->toArray();
        $this->assertModelData($supplierCritical->toArray(), $dbSupplierCritical);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierCritical()
    {
        $supplierCritical = $this->makeSupplierCritical();
        $fakeSupplierCritical = $this->fakeSupplierCriticalData();
        $updatedSupplierCritical = $this->supplierCriticalRepo->update($fakeSupplierCritical, $supplierCritical->id);
        $this->assertModelData($fakeSupplierCritical, $updatedSupplierCritical->toArray());
        $dbSupplierCritical = $this->supplierCriticalRepo->find($supplierCritical->id);
        $this->assertModelData($fakeSupplierCritical, $dbSupplierCritical->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierCritical()
    {
        $supplierCritical = $this->makeSupplierCritical();
        $resp = $this->supplierCriticalRepo->delete($supplierCritical->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierCritical::find($supplierCritical->id), 'SupplierCritical should not exist in DB');
    }
}
