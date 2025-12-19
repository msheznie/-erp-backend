<?php

use App\Models\StockAdjustmentDetailsRefferedBack;
use App\Repositories\StockAdjustmentDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeStockAdjustmentDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockAdjustmentDetailsRefferedBackRepository
     */
    protected $stockAdjustmentDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockAdjustmentDetailsRefferedBackRepo = App::make(StockAdjustmentDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->fakeStockAdjustmentDetailsRefferedBackData();
        $createdStockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepo->create($stockAdjustmentDetailsRefferedBack);
        $createdStockAdjustmentDetailsRefferedBack = $createdStockAdjustmentDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockAdjustmentDetailsRefferedBack);
        $this->assertNotNull($createdStockAdjustmentDetailsRefferedBack['id'], 'Created StockAdjustmentDetailsRefferedBack must have id specified');
        $this->assertNotNull(StockAdjustmentDetailsRefferedBack::find($createdStockAdjustmentDetailsRefferedBack['id']), 'StockAdjustmentDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($stockAdjustmentDetailsRefferedBack, $createdStockAdjustmentDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->makeStockAdjustmentDetailsRefferedBack();
        $dbStockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepo->find($stockAdjustmentDetailsRefferedBack->id);
        $dbStockAdjustmentDetailsRefferedBack = $dbStockAdjustmentDetailsRefferedBack->toArray();
        $this->assertModelData($stockAdjustmentDetailsRefferedBack->toArray(), $dbStockAdjustmentDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->makeStockAdjustmentDetailsRefferedBack();
        $fakeStockAdjustmentDetailsRefferedBack = $this->fakeStockAdjustmentDetailsRefferedBackData();
        $updatedStockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepo->update($fakeStockAdjustmentDetailsRefferedBack, $stockAdjustmentDetailsRefferedBack->id);
        $this->assertModelData($fakeStockAdjustmentDetailsRefferedBack, $updatedStockAdjustmentDetailsRefferedBack->toArray());
        $dbStockAdjustmentDetailsRefferedBack = $this->stockAdjustmentDetailsRefferedBackRepo->find($stockAdjustmentDetailsRefferedBack->id);
        $this->assertModelData($fakeStockAdjustmentDetailsRefferedBack, $dbStockAdjustmentDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->makeStockAdjustmentDetailsRefferedBack();
        $resp = $this->stockAdjustmentDetailsRefferedBackRepo->delete($stockAdjustmentDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(StockAdjustmentDetailsRefferedBack::find($stockAdjustmentDetailsRefferedBack->id), 'StockAdjustmentDetailsRefferedBack should not exist in DB');
    }
}
