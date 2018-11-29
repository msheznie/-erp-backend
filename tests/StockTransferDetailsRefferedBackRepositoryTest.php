<?php

use App\Models\StockTransferDetailsRefferedBack;
use App\Repositories\StockTransferDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeStockTransferDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockTransferDetailsRefferedBackRepository
     */
    protected $stockTransferDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockTransferDetailsRefferedBackRepo = App::make(StockTransferDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->fakeStockTransferDetailsRefferedBackData();
        $createdStockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepo->create($stockTransferDetailsRefferedBack);
        $createdStockTransferDetailsRefferedBack = $createdStockTransferDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockTransferDetailsRefferedBack);
        $this->assertNotNull($createdStockTransferDetailsRefferedBack['id'], 'Created StockTransferDetailsRefferedBack must have id specified');
        $this->assertNotNull(StockTransferDetailsRefferedBack::find($createdStockTransferDetailsRefferedBack['id']), 'StockTransferDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($stockTransferDetailsRefferedBack, $createdStockTransferDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->makeStockTransferDetailsRefferedBack();
        $dbStockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepo->find($stockTransferDetailsRefferedBack->id);
        $dbStockTransferDetailsRefferedBack = $dbStockTransferDetailsRefferedBack->toArray();
        $this->assertModelData($stockTransferDetailsRefferedBack->toArray(), $dbStockTransferDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->makeStockTransferDetailsRefferedBack();
        $fakeStockTransferDetailsRefferedBack = $this->fakeStockTransferDetailsRefferedBackData();
        $updatedStockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepo->update($fakeStockTransferDetailsRefferedBack, $stockTransferDetailsRefferedBack->id);
        $this->assertModelData($fakeStockTransferDetailsRefferedBack, $updatedStockTransferDetailsRefferedBack->toArray());
        $dbStockTransferDetailsRefferedBack = $this->stockTransferDetailsRefferedBackRepo->find($stockTransferDetailsRefferedBack->id);
        $this->assertModelData($fakeStockTransferDetailsRefferedBack, $dbStockTransferDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->makeStockTransferDetailsRefferedBack();
        $resp = $this->stockTransferDetailsRefferedBackRepo->delete($stockTransferDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(StockTransferDetailsRefferedBack::find($stockTransferDetailsRefferedBack->id), 'StockTransferDetailsRefferedBack should not exist in DB');
    }
}
