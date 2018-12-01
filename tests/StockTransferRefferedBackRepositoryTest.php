<?php

use App\Models\StockTransferRefferedBack;
use App\Repositories\StockTransferRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferRefferedBackRepositoryTest extends TestCase
{
    use MakeStockTransferRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockTransferRefferedBackRepository
     */
    protected $stockTransferRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockTransferRefferedBackRepo = App::make(StockTransferRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->fakeStockTransferRefferedBackData();
        $createdStockTransferRefferedBack = $this->stockTransferRefferedBackRepo->create($stockTransferRefferedBack);
        $createdStockTransferRefferedBack = $createdStockTransferRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockTransferRefferedBack);
        $this->assertNotNull($createdStockTransferRefferedBack['id'], 'Created StockTransferRefferedBack must have id specified');
        $this->assertNotNull(StockTransferRefferedBack::find($createdStockTransferRefferedBack['id']), 'StockTransferRefferedBack with given id must be in DB');
        $this->assertModelData($stockTransferRefferedBack, $createdStockTransferRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->makeStockTransferRefferedBack();
        $dbStockTransferRefferedBack = $this->stockTransferRefferedBackRepo->find($stockTransferRefferedBack->id);
        $dbStockTransferRefferedBack = $dbStockTransferRefferedBack->toArray();
        $this->assertModelData($stockTransferRefferedBack->toArray(), $dbStockTransferRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->makeStockTransferRefferedBack();
        $fakeStockTransferRefferedBack = $this->fakeStockTransferRefferedBackData();
        $updatedStockTransferRefferedBack = $this->stockTransferRefferedBackRepo->update($fakeStockTransferRefferedBack, $stockTransferRefferedBack->id);
        $this->assertModelData($fakeStockTransferRefferedBack, $updatedStockTransferRefferedBack->toArray());
        $dbStockTransferRefferedBack = $this->stockTransferRefferedBackRepo->find($stockTransferRefferedBack->id);
        $this->assertModelData($fakeStockTransferRefferedBack, $dbStockTransferRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->makeStockTransferRefferedBack();
        $resp = $this->stockTransferRefferedBackRepo->delete($stockTransferRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(StockTransferRefferedBack::find($stockTransferRefferedBack->id), 'StockTransferRefferedBack should not exist in DB');
    }
}
