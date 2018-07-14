<?php

use App\Models\StockTransfer;
use App\Repositories\StockTransferRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferRepositoryTest extends TestCase
{
    use MakeStockTransferTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockTransferRepository
     */
    protected $stockTransferRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockTransferRepo = App::make(StockTransferRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockTransfer()
    {
        $stockTransfer = $this->fakeStockTransferData();
        $createdStockTransfer = $this->stockTransferRepo->create($stockTransfer);
        $createdStockTransfer = $createdStockTransfer->toArray();
        $this->assertArrayHasKey('id', $createdStockTransfer);
        $this->assertNotNull($createdStockTransfer['id'], 'Created StockTransfer must have id specified');
        $this->assertNotNull(StockTransfer::find($createdStockTransfer['id']), 'StockTransfer with given id must be in DB');
        $this->assertModelData($stockTransfer, $createdStockTransfer);
    }

    /**
     * @test read
     */
    public function testReadStockTransfer()
    {
        $stockTransfer = $this->makeStockTransfer();
        $dbStockTransfer = $this->stockTransferRepo->find($stockTransfer->id);
        $dbStockTransfer = $dbStockTransfer->toArray();
        $this->assertModelData($stockTransfer->toArray(), $dbStockTransfer);
    }

    /**
     * @test update
     */
    public function testUpdateStockTransfer()
    {
        $stockTransfer = $this->makeStockTransfer();
        $fakeStockTransfer = $this->fakeStockTransferData();
        $updatedStockTransfer = $this->stockTransferRepo->update($fakeStockTransfer, $stockTransfer->id);
        $this->assertModelData($fakeStockTransfer, $updatedStockTransfer->toArray());
        $dbStockTransfer = $this->stockTransferRepo->find($stockTransfer->id);
        $this->assertModelData($fakeStockTransfer, $dbStockTransfer->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockTransfer()
    {
        $stockTransfer = $this->makeStockTransfer();
        $resp = $this->stockTransferRepo->delete($stockTransfer->id);
        $this->assertTrue($resp);
        $this->assertNull(StockTransfer::find($stockTransfer->id), 'StockTransfer should not exist in DB');
    }
}
