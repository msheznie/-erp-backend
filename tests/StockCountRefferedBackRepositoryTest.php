<?php namespace Tests\Repositories;

use App\Models\StockCountRefferedBack;
use App\Repositories\StockCountRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class StockCountRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockCountRefferedBackRepository
     */
    protected $stockCountRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->stockCountRefferedBackRepo = \App::make(StockCountRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->make()->toArray();

        $createdStockCountRefferedBack = $this->stockCountRefferedBackRepo->create($stockCountRefferedBack);

        $createdStockCountRefferedBack = $createdStockCountRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockCountRefferedBack);
        $this->assertNotNull($createdStockCountRefferedBack['id'], 'Created StockCountRefferedBack must have id specified');
        $this->assertNotNull(StockCountRefferedBack::find($createdStockCountRefferedBack['id']), 'StockCountRefferedBack with given id must be in DB');
        $this->assertModelData($stockCountRefferedBack, $createdStockCountRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->create();

        $dbStockCountRefferedBack = $this->stockCountRefferedBackRepo->find($stockCountRefferedBack->id);

        $dbStockCountRefferedBack = $dbStockCountRefferedBack->toArray();
        $this->assertModelData($stockCountRefferedBack->toArray(), $dbStockCountRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->create();
        $fakeStockCountRefferedBack = factory(StockCountRefferedBack::class)->make()->toArray();

        $updatedStockCountRefferedBack = $this->stockCountRefferedBackRepo->update($fakeStockCountRefferedBack, $stockCountRefferedBack->id);

        $this->assertModelData($fakeStockCountRefferedBack, $updatedStockCountRefferedBack->toArray());
        $dbStockCountRefferedBack = $this->stockCountRefferedBackRepo->find($stockCountRefferedBack->id);
        $this->assertModelData($fakeStockCountRefferedBack, $dbStockCountRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->create();

        $resp = $this->stockCountRefferedBackRepo->delete($stockCountRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(StockCountRefferedBack::find($stockCountRefferedBack->id), 'StockCountRefferedBack should not exist in DB');
    }
}
