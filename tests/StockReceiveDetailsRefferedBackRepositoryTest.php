<?php

use App\Models\StockReceiveDetailsRefferedBack;
use App\Repositories\StockReceiveDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeStockReceiveDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockReceiveDetailsRefferedBackRepository
     */
    protected $stockReceiveDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockReceiveDetailsRefferedBackRepo = App::make(StockReceiveDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->fakeStockReceiveDetailsRefferedBackData();
        $createdStockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepo->create($stockReceiveDetailsRefferedBack);
        $createdStockReceiveDetailsRefferedBack = $createdStockReceiveDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockReceiveDetailsRefferedBack);
        $this->assertNotNull($createdStockReceiveDetailsRefferedBack['id'], 'Created StockReceiveDetailsRefferedBack must have id specified');
        $this->assertNotNull(StockReceiveDetailsRefferedBack::find($createdStockReceiveDetailsRefferedBack['id']), 'StockReceiveDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($stockReceiveDetailsRefferedBack, $createdStockReceiveDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->makeStockReceiveDetailsRefferedBack();
        $dbStockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepo->find($stockReceiveDetailsRefferedBack->id);
        $dbStockReceiveDetailsRefferedBack = $dbStockReceiveDetailsRefferedBack->toArray();
        $this->assertModelData($stockReceiveDetailsRefferedBack->toArray(), $dbStockReceiveDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->makeStockReceiveDetailsRefferedBack();
        $fakeStockReceiveDetailsRefferedBack = $this->fakeStockReceiveDetailsRefferedBackData();
        $updatedStockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepo->update($fakeStockReceiveDetailsRefferedBack, $stockReceiveDetailsRefferedBack->id);
        $this->assertModelData($fakeStockReceiveDetailsRefferedBack, $updatedStockReceiveDetailsRefferedBack->toArray());
        $dbStockReceiveDetailsRefferedBack = $this->stockReceiveDetailsRefferedBackRepo->find($stockReceiveDetailsRefferedBack->id);
        $this->assertModelData($fakeStockReceiveDetailsRefferedBack, $dbStockReceiveDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->makeStockReceiveDetailsRefferedBack();
        $resp = $this->stockReceiveDetailsRefferedBackRepo->delete($stockReceiveDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(StockReceiveDetailsRefferedBack::find($stockReceiveDetailsRefferedBack->id), 'StockReceiveDetailsRefferedBack should not exist in DB');
    }
}
