<?php

use App\Models\WarehouseMaster;
use App\Repositories\WarehouseMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseMasterRepositoryTest extends TestCase
{
    use MakeWarehouseMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WarehouseMasterRepository
     */
    protected $warehouseMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->warehouseMasterRepo = App::make(WarehouseMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWarehouseMaster()
    {
        $warehouseMaster = $this->fakeWarehouseMasterData();
        $createdWarehouseMaster = $this->warehouseMasterRepo->create($warehouseMaster);
        $createdWarehouseMaster = $createdWarehouseMaster->toArray();
        $this->assertArrayHasKey('id', $createdWarehouseMaster);
        $this->assertNotNull($createdWarehouseMaster['id'], 'Created WarehouseMaster must have id specified');
        $this->assertNotNull(WarehouseMaster::find($createdWarehouseMaster['id']), 'WarehouseMaster with given id must be in DB');
        $this->assertModelData($warehouseMaster, $createdWarehouseMaster);
    }

    /**
     * @test read
     */
    public function testReadWarehouseMaster()
    {
        $warehouseMaster = $this->makeWarehouseMaster();
        $dbWarehouseMaster = $this->warehouseMasterRepo->find($warehouseMaster->id);
        $dbWarehouseMaster = $dbWarehouseMaster->toArray();
        $this->assertModelData($warehouseMaster->toArray(), $dbWarehouseMaster);
    }

    /**
     * @test update
     */
    public function testUpdateWarehouseMaster()
    {
        $warehouseMaster = $this->makeWarehouseMaster();
        $fakeWarehouseMaster = $this->fakeWarehouseMasterData();
        $updatedWarehouseMaster = $this->warehouseMasterRepo->update($fakeWarehouseMaster, $warehouseMaster->id);
        $this->assertModelData($fakeWarehouseMaster, $updatedWarehouseMaster->toArray());
        $dbWarehouseMaster = $this->warehouseMasterRepo->find($warehouseMaster->id);
        $this->assertModelData($fakeWarehouseMaster, $dbWarehouseMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWarehouseMaster()
    {
        $warehouseMaster = $this->makeWarehouseMaster();
        $resp = $this->warehouseMasterRepo->delete($warehouseMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(WarehouseMaster::find($warehouseMaster->id), 'WarehouseMaster should not exist in DB');
    }
}
