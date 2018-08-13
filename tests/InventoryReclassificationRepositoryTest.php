<?php

use App\Models\InventoryReclassification;
use App\Repositories\InventoryReclassificationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryReclassificationRepositoryTest extends TestCase
{
    use MakeInventoryReclassificationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InventoryReclassificationRepository
     */
    protected $inventoryReclassificationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->inventoryReclassificationRepo = App::make(InventoryReclassificationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateInventoryReclassification()
    {
        $inventoryReclassification = $this->fakeInventoryReclassificationData();
        $createdInventoryReclassification = $this->inventoryReclassificationRepo->create($inventoryReclassification);
        $createdInventoryReclassification = $createdInventoryReclassification->toArray();
        $this->assertArrayHasKey('id', $createdInventoryReclassification);
        $this->assertNotNull($createdInventoryReclassification['id'], 'Created InventoryReclassification must have id specified');
        $this->assertNotNull(InventoryReclassification::find($createdInventoryReclassification['id']), 'InventoryReclassification with given id must be in DB');
        $this->assertModelData($inventoryReclassification, $createdInventoryReclassification);
    }

    /**
     * @test read
     */
    public function testReadInventoryReclassification()
    {
        $inventoryReclassification = $this->makeInventoryReclassification();
        $dbInventoryReclassification = $this->inventoryReclassificationRepo->find($inventoryReclassification->id);
        $dbInventoryReclassification = $dbInventoryReclassification->toArray();
        $this->assertModelData($inventoryReclassification->toArray(), $dbInventoryReclassification);
    }

    /**
     * @test update
     */
    public function testUpdateInventoryReclassification()
    {
        $inventoryReclassification = $this->makeInventoryReclassification();
        $fakeInventoryReclassification = $this->fakeInventoryReclassificationData();
        $updatedInventoryReclassification = $this->inventoryReclassificationRepo->update($fakeInventoryReclassification, $inventoryReclassification->id);
        $this->assertModelData($fakeInventoryReclassification, $updatedInventoryReclassification->toArray());
        $dbInventoryReclassification = $this->inventoryReclassificationRepo->find($inventoryReclassification->id);
        $this->assertModelData($fakeInventoryReclassification, $dbInventoryReclassification->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteInventoryReclassification()
    {
        $inventoryReclassification = $this->makeInventoryReclassification();
        $resp = $this->inventoryReclassificationRepo->delete($inventoryReclassification->id);
        $this->assertTrue($resp);
        $this->assertNull(InventoryReclassification::find($inventoryReclassification->id), 'InventoryReclassification should not exist in DB');
    }
}
