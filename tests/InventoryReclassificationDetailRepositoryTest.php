<?php

use App\Models\InventoryReclassificationDetail;
use App\Repositories\InventoryReclassificationDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryReclassificationDetailRepositoryTest extends TestCase
{
    use MakeInventoryReclassificationDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InventoryReclassificationDetailRepository
     */
    protected $inventoryReclassificationDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->inventoryReclassificationDetailRepo = App::make(InventoryReclassificationDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->fakeInventoryReclassificationDetailData();
        $createdInventoryReclassificationDetail = $this->inventoryReclassificationDetailRepo->create($inventoryReclassificationDetail);
        $createdInventoryReclassificationDetail = $createdInventoryReclassificationDetail->toArray();
        $this->assertArrayHasKey('id', $createdInventoryReclassificationDetail);
        $this->assertNotNull($createdInventoryReclassificationDetail['id'], 'Created InventoryReclassificationDetail must have id specified');
        $this->assertNotNull(InventoryReclassificationDetail::find($createdInventoryReclassificationDetail['id']), 'InventoryReclassificationDetail with given id must be in DB');
        $this->assertModelData($inventoryReclassificationDetail, $createdInventoryReclassificationDetail);
    }

    /**
     * @test read
     */
    public function testReadInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->makeInventoryReclassificationDetail();
        $dbInventoryReclassificationDetail = $this->inventoryReclassificationDetailRepo->find($inventoryReclassificationDetail->id);
        $dbInventoryReclassificationDetail = $dbInventoryReclassificationDetail->toArray();
        $this->assertModelData($inventoryReclassificationDetail->toArray(), $dbInventoryReclassificationDetail);
    }

    /**
     * @test update
     */
    public function testUpdateInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->makeInventoryReclassificationDetail();
        $fakeInventoryReclassificationDetail = $this->fakeInventoryReclassificationDetailData();
        $updatedInventoryReclassificationDetail = $this->inventoryReclassificationDetailRepo->update($fakeInventoryReclassificationDetail, $inventoryReclassificationDetail->id);
        $this->assertModelData($fakeInventoryReclassificationDetail, $updatedInventoryReclassificationDetail->toArray());
        $dbInventoryReclassificationDetail = $this->inventoryReclassificationDetailRepo->find($inventoryReclassificationDetail->id);
        $this->assertModelData($fakeInventoryReclassificationDetail, $dbInventoryReclassificationDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->makeInventoryReclassificationDetail();
        $resp = $this->inventoryReclassificationDetailRepo->delete($inventoryReclassificationDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(InventoryReclassificationDetail::find($inventoryReclassificationDetail->id), 'InventoryReclassificationDetail should not exist in DB');
    }
}
