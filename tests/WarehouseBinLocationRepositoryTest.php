<?php

use App\Models\WarehouseBinLocation;
use App\Repositories\WarehouseBinLocationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseBinLocationRepositoryTest extends TestCase
{
    use MakeWarehouseBinLocationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WarehouseBinLocationRepository
     */
    protected $warehouseBinLocationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->warehouseBinLocationRepo = App::make(WarehouseBinLocationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->fakeWarehouseBinLocationData();
        $createdWarehouseBinLocation = $this->warehouseBinLocationRepo->create($warehouseBinLocation);
        $createdWarehouseBinLocation = $createdWarehouseBinLocation->toArray();
        $this->assertArrayHasKey('id', $createdWarehouseBinLocation);
        $this->assertNotNull($createdWarehouseBinLocation['id'], 'Created WarehouseBinLocation must have id specified');
        $this->assertNotNull(WarehouseBinLocation::find($createdWarehouseBinLocation['id']), 'WarehouseBinLocation with given id must be in DB');
        $this->assertModelData($warehouseBinLocation, $createdWarehouseBinLocation);
    }

    /**
     * @test read
     */
    public function testReadWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->makeWarehouseBinLocation();
        $dbWarehouseBinLocation = $this->warehouseBinLocationRepo->find($warehouseBinLocation->id);
        $dbWarehouseBinLocation = $dbWarehouseBinLocation->toArray();
        $this->assertModelData($warehouseBinLocation->toArray(), $dbWarehouseBinLocation);
    }

    /**
     * @test update
     */
    public function testUpdateWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->makeWarehouseBinLocation();
        $fakeWarehouseBinLocation = $this->fakeWarehouseBinLocationData();
        $updatedWarehouseBinLocation = $this->warehouseBinLocationRepo->update($fakeWarehouseBinLocation, $warehouseBinLocation->id);
        $this->assertModelData($fakeWarehouseBinLocation, $updatedWarehouseBinLocation->toArray());
        $dbWarehouseBinLocation = $this->warehouseBinLocationRepo->find($warehouseBinLocation->id);
        $this->assertModelData($fakeWarehouseBinLocation, $dbWarehouseBinLocation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->makeWarehouseBinLocation();
        $resp = $this->warehouseBinLocationRepo->delete($warehouseBinLocation->id);
        $this->assertTrue($resp);
        $this->assertNull(WarehouseBinLocation::find($warehouseBinLocation->id), 'WarehouseBinLocation should not exist in DB');
    }
}
