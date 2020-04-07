<?php namespace Tests\Repositories;

use App\Models\WarehouseSubLevels;
use App\Repositories\WarehouseSubLevelsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class WarehouseSubLevelsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var WarehouseSubLevelsRepository
     */
    protected $warehouseSubLevelsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->warehouseSubLevelsRepo = \App::make(WarehouseSubLevelsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->make()->toArray();

        $createdWarehouseSubLevels = $this->warehouseSubLevelsRepo->create($warehouseSubLevels);

        $createdWarehouseSubLevels = $createdWarehouseSubLevels->toArray();
        $this->assertArrayHasKey('id', $createdWarehouseSubLevels);
        $this->assertNotNull($createdWarehouseSubLevels['id'], 'Created WarehouseSubLevels must have id specified');
        $this->assertNotNull(WarehouseSubLevels::find($createdWarehouseSubLevels['id']), 'WarehouseSubLevels with given id must be in DB');
        $this->assertModelData($warehouseSubLevels, $createdWarehouseSubLevels);
    }

    /**
     * @test read
     */
    public function test_read_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->create();

        $dbWarehouseSubLevels = $this->warehouseSubLevelsRepo->find($warehouseSubLevels->id);

        $dbWarehouseSubLevels = $dbWarehouseSubLevels->toArray();
        $this->assertModelData($warehouseSubLevels->toArray(), $dbWarehouseSubLevels);
    }

    /**
     * @test update
     */
    public function test_update_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->create();
        $fakeWarehouseSubLevels = factory(WarehouseSubLevels::class)->make()->toArray();

        $updatedWarehouseSubLevels = $this->warehouseSubLevelsRepo->update($fakeWarehouseSubLevels, $warehouseSubLevels->id);

        $this->assertModelData($fakeWarehouseSubLevels, $updatedWarehouseSubLevels->toArray());
        $dbWarehouseSubLevels = $this->warehouseSubLevelsRepo->find($warehouseSubLevels->id);
        $this->assertModelData($fakeWarehouseSubLevels, $dbWarehouseSubLevels->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_warehouse_sub_levels()
    {
        $warehouseSubLevels = factory(WarehouseSubLevels::class)->create();

        $resp = $this->warehouseSubLevelsRepo->delete($warehouseSubLevels->id);

        $this->assertTrue($resp);
        $this->assertNull(WarehouseSubLevels::find($warehouseSubLevels->id), 'WarehouseSubLevels should not exist in DB');
    }
}
