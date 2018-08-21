<?php

use App\Models\StockAdjustment;
use App\Repositories\StockAdjustmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentRepositoryTest extends TestCase
{
    use MakeStockAdjustmentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockAdjustmentRepository
     */
    protected $stockAdjustmentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockAdjustmentRepo = App::make(StockAdjustmentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockAdjustment()
    {
        $stockAdjustment = $this->fakeStockAdjustmentData();
        $createdStockAdjustment = $this->stockAdjustmentRepo->create($stockAdjustment);
        $createdStockAdjustment = $createdStockAdjustment->toArray();
        $this->assertArrayHasKey('id', $createdStockAdjustment);
        $this->assertNotNull($createdStockAdjustment['id'], 'Created StockAdjustment must have id specified');
        $this->assertNotNull(StockAdjustment::find($createdStockAdjustment['id']), 'StockAdjustment with given id must be in DB');
        $this->assertModelData($stockAdjustment, $createdStockAdjustment);
    }

    /**
     * @test read
     */
    public function testReadStockAdjustment()
    {
        $stockAdjustment = $this->makeStockAdjustment();
        $dbStockAdjustment = $this->stockAdjustmentRepo->find($stockAdjustment->id);
        $dbStockAdjustment = $dbStockAdjustment->toArray();
        $this->assertModelData($stockAdjustment->toArray(), $dbStockAdjustment);
    }

    /**
     * @test update
     */
    public function testUpdateStockAdjustment()
    {
        $stockAdjustment = $this->makeStockAdjustment();
        $fakeStockAdjustment = $this->fakeStockAdjustmentData();
        $updatedStockAdjustment = $this->stockAdjustmentRepo->update($fakeStockAdjustment, $stockAdjustment->id);
        $this->assertModelData($fakeStockAdjustment, $updatedStockAdjustment->toArray());
        $dbStockAdjustment = $this->stockAdjustmentRepo->find($stockAdjustment->id);
        $this->assertModelData($fakeStockAdjustment, $dbStockAdjustment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockAdjustment()
    {
        $stockAdjustment = $this->makeStockAdjustment();
        $resp = $this->stockAdjustmentRepo->delete($stockAdjustment->id);
        $this->assertTrue($resp);
        $this->assertNull(StockAdjustment::find($stockAdjustment->id), 'StockAdjustment should not exist in DB');
    }
}
