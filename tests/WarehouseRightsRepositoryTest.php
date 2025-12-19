<?php namespace Tests\Repositories;

use App\Models\WarehouseRights;
use App\Repositories\WarehouseRightsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeWarehouseRightsTrait;
use Tests\ApiTestTrait;

class WarehouseRightsRepositoryTest extends TestCase
{
    use MakeWarehouseRightsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WarehouseRightsRepository
     */
    protected $warehouseRightsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->warehouseRightsRepo = \App::make(WarehouseRightsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_warehouse_rights()
    {
        $warehouseRights = $this->fakeWarehouseRightsData();
        $createdWarehouseRights = $this->warehouseRightsRepo->create($warehouseRights);
        $createdWarehouseRights = $createdWarehouseRights->toArray();
        $this->assertArrayHasKey('id', $createdWarehouseRights);
        $this->assertNotNull($createdWarehouseRights['id'], 'Created WarehouseRights must have id specified');
        $this->assertNotNull(WarehouseRights::find($createdWarehouseRights['id']), 'WarehouseRights with given id must be in DB');
        $this->assertModelData($warehouseRights, $createdWarehouseRights);
    }

    /**
     * @test read
     */
    public function test_read_warehouse_rights()
    {
        $warehouseRights = $this->makeWarehouseRights();
        $dbWarehouseRights = $this->warehouseRightsRepo->find($warehouseRights->id);
        $dbWarehouseRights = $dbWarehouseRights->toArray();
        $this->assertModelData($warehouseRights->toArray(), $dbWarehouseRights);
    }

    /**
     * @test update
     */
    public function test_update_warehouse_rights()
    {
        $warehouseRights = $this->makeWarehouseRights();
        $fakeWarehouseRights = $this->fakeWarehouseRightsData();
        $updatedWarehouseRights = $this->warehouseRightsRepo->update($fakeWarehouseRights, $warehouseRights->id);
        $this->assertModelData($fakeWarehouseRights, $updatedWarehouseRights->toArray());
        $dbWarehouseRights = $this->warehouseRightsRepo->find($warehouseRights->id);
        $this->assertModelData($fakeWarehouseRights, $dbWarehouseRights->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_warehouse_rights()
    {
        $warehouseRights = $this->makeWarehouseRights();
        $resp = $this->warehouseRightsRepo->delete($warehouseRights->id);
        $this->assertTrue($resp);
        $this->assertNull(WarehouseRights::find($warehouseRights->id), 'WarehouseRights should not exist in DB');
    }
}
