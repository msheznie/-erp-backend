<?php

use App\Models\StockReceiveRefferedBack;
use App\Repositories\StockReceiveRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveRefferedBackRepositoryTest extends TestCase
{
    use MakeStockReceiveRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockReceiveRefferedBackRepository
     */
    protected $stockReceiveRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockReceiveRefferedBackRepo = App::make(StockReceiveRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->fakeStockReceiveRefferedBackData();
        $createdStockReceiveRefferedBack = $this->stockReceiveRefferedBackRepo->create($stockReceiveRefferedBack);
        $createdStockReceiveRefferedBack = $createdStockReceiveRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockReceiveRefferedBack);
        $this->assertNotNull($createdStockReceiveRefferedBack['id'], 'Created StockReceiveRefferedBack must have id specified');
        $this->assertNotNull(StockReceiveRefferedBack::find($createdStockReceiveRefferedBack['id']), 'StockReceiveRefferedBack with given id must be in DB');
        $this->assertModelData($stockReceiveRefferedBack, $createdStockReceiveRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->makeStockReceiveRefferedBack();
        $dbStockReceiveRefferedBack = $this->stockReceiveRefferedBackRepo->find($stockReceiveRefferedBack->id);
        $dbStockReceiveRefferedBack = $dbStockReceiveRefferedBack->toArray();
        $this->assertModelData($stockReceiveRefferedBack->toArray(), $dbStockReceiveRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->makeStockReceiveRefferedBack();
        $fakeStockReceiveRefferedBack = $this->fakeStockReceiveRefferedBackData();
        $updatedStockReceiveRefferedBack = $this->stockReceiveRefferedBackRepo->update($fakeStockReceiveRefferedBack, $stockReceiveRefferedBack->id);
        $this->assertModelData($fakeStockReceiveRefferedBack, $updatedStockReceiveRefferedBack->toArray());
        $dbStockReceiveRefferedBack = $this->stockReceiveRefferedBackRepo->find($stockReceiveRefferedBack->id);
        $this->assertModelData($fakeStockReceiveRefferedBack, $dbStockReceiveRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->makeStockReceiveRefferedBack();
        $resp = $this->stockReceiveRefferedBackRepo->delete($stockReceiveRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(StockReceiveRefferedBack::find($stockReceiveRefferedBack->id), 'StockReceiveRefferedBack should not exist in DB');
    }
}
