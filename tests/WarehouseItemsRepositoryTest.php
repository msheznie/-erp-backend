<?php

use App\Models\WarehouseItems;
use App\Repositories\WarehouseItemsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseItemsRepositoryTest extends TestCase
{
    use MakeWarehouseItemsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var WarehouseItemsRepository
     */
    protected $warehouseItemsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->warehouseItemsRepo = App::make(WarehouseItemsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateWarehouseItems()
    {
        $warehouseItems = $this->fakeWarehouseItemsData();
        $createdWarehouseItems = $this->warehouseItemsRepo->create($warehouseItems);
        $createdWarehouseItems = $createdWarehouseItems->toArray();
        $this->assertArrayHasKey('id', $createdWarehouseItems);
        $this->assertNotNull($createdWarehouseItems['id'], 'Created WarehouseItems must have id specified');
        $this->assertNotNull(WarehouseItems::find($createdWarehouseItems['id']), 'WarehouseItems with given id must be in DB');
        $this->assertModelData($warehouseItems, $createdWarehouseItems);
    }

    /**
     * @test read
     */
    public function testReadWarehouseItems()
    {
        $warehouseItems = $this->makeWarehouseItems();
        $dbWarehouseItems = $this->warehouseItemsRepo->find($warehouseItems->id);
        $dbWarehouseItems = $dbWarehouseItems->toArray();
        $this->assertModelData($warehouseItems->toArray(), $dbWarehouseItems);
    }

    /**
     * @test update
     */
    public function testUpdateWarehouseItems()
    {
        $warehouseItems = $this->makeWarehouseItems();
        $fakeWarehouseItems = $this->fakeWarehouseItemsData();
        $updatedWarehouseItems = $this->warehouseItemsRepo->update($fakeWarehouseItems, $warehouseItems->id);
        $this->assertModelData($fakeWarehouseItems, $updatedWarehouseItems->toArray());
        $dbWarehouseItems = $this->warehouseItemsRepo->find($warehouseItems->id);
        $this->assertModelData($fakeWarehouseItems, $dbWarehouseItems->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteWarehouseItems()
    {
        $warehouseItems = $this->makeWarehouseItems();
        $resp = $this->warehouseItemsRepo->delete($warehouseItems->id);
        $this->assertTrue($resp);
        $this->assertNull(WarehouseItems::find($warehouseItems->id), 'WarehouseItems should not exist in DB');
    }
}
