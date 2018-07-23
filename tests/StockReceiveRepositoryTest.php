<?php

use App\Models\StockReceive;
use App\Repositories\StockReceiveRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveRepositoryTest extends TestCase
{
    use MakeStockReceiveTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockReceiveRepository
     */
    protected $stockReceiveRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockReceiveRepo = App::make(StockReceiveRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockReceive()
    {
        $stockReceive = $this->fakeStockReceiveData();
        $createdStockReceive = $this->stockReceiveRepo->create($stockReceive);
        $createdStockReceive = $createdStockReceive->toArray();
        $this->assertArrayHasKey('id', $createdStockReceive);
        $this->assertNotNull($createdStockReceive['id'], 'Created StockReceive must have id specified');
        $this->assertNotNull(StockReceive::find($createdStockReceive['id']), 'StockReceive with given id must be in DB');
        $this->assertModelData($stockReceive, $createdStockReceive);
    }

    /**
     * @test read
     */
    public function testReadStockReceive()
    {
        $stockReceive = $this->makeStockReceive();
        $dbStockReceive = $this->stockReceiveRepo->find($stockReceive->id);
        $dbStockReceive = $dbStockReceive->toArray();
        $this->assertModelData($stockReceive->toArray(), $dbStockReceive);
    }

    /**
     * @test update
     */
    public function testUpdateStockReceive()
    {
        $stockReceive = $this->makeStockReceive();
        $fakeStockReceive = $this->fakeStockReceiveData();
        $updatedStockReceive = $this->stockReceiveRepo->update($fakeStockReceive, $stockReceive->id);
        $this->assertModelData($fakeStockReceive, $updatedStockReceive->toArray());
        $dbStockReceive = $this->stockReceiveRepo->find($stockReceive->id);
        $this->assertModelData($fakeStockReceive, $dbStockReceive->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockReceive()
    {
        $stockReceive = $this->makeStockReceive();
        $resp = $this->stockReceiveRepo->delete($stockReceive->id);
        $this->assertTrue($resp);
        $this->assertNull(StockReceive::find($stockReceive->id), 'StockReceive should not exist in DB');
    }
}
