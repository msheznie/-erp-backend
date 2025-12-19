<?php

use App\Models\StockAdjustmentRefferedBack;
use App\Repositories\StockAdjustmentRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentRefferedBackRepositoryTest extends TestCase
{
    use MakeStockAdjustmentRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockAdjustmentRefferedBackRepository
     */
    protected $stockAdjustmentRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockAdjustmentRefferedBackRepo = App::make(StockAdjustmentRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->fakeStockAdjustmentRefferedBackData();
        $createdStockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepo->create($stockAdjustmentRefferedBack);
        $createdStockAdjustmentRefferedBack = $createdStockAdjustmentRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockAdjustmentRefferedBack);
        $this->assertNotNull($createdStockAdjustmentRefferedBack['id'], 'Created StockAdjustmentRefferedBack must have id specified');
        $this->assertNotNull(StockAdjustmentRefferedBack::find($createdStockAdjustmentRefferedBack['id']), 'StockAdjustmentRefferedBack with given id must be in DB');
        $this->assertModelData($stockAdjustmentRefferedBack, $createdStockAdjustmentRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->makeStockAdjustmentRefferedBack();
        $dbStockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepo->find($stockAdjustmentRefferedBack->id);
        $dbStockAdjustmentRefferedBack = $dbStockAdjustmentRefferedBack->toArray();
        $this->assertModelData($stockAdjustmentRefferedBack->toArray(), $dbStockAdjustmentRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->makeStockAdjustmentRefferedBack();
        $fakeStockAdjustmentRefferedBack = $this->fakeStockAdjustmentRefferedBackData();
        $updatedStockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepo->update($fakeStockAdjustmentRefferedBack, $stockAdjustmentRefferedBack->id);
        $this->assertModelData($fakeStockAdjustmentRefferedBack, $updatedStockAdjustmentRefferedBack->toArray());
        $dbStockAdjustmentRefferedBack = $this->stockAdjustmentRefferedBackRepo->find($stockAdjustmentRefferedBack->id);
        $this->assertModelData($fakeStockAdjustmentRefferedBack, $dbStockAdjustmentRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->makeStockAdjustmentRefferedBack();
        $resp = $this->stockAdjustmentRefferedBackRepo->delete($stockAdjustmentRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(StockAdjustmentRefferedBack::find($stockAdjustmentRefferedBack->id), 'StockAdjustmentRefferedBack should not exist in DB');
    }
}
