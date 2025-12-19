<?php namespace Tests\Repositories;

use App\Models\StockCount;
use App\Repositories\StockCountRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class StockCountRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockCountRepository
     */
    protected $stockCountRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->stockCountRepo = \App::make(StockCountRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_stock_count()
    {
        $stockCount = factory(StockCount::class)->make()->toArray();

        $createdStockCount = $this->stockCountRepo->create($stockCount);

        $createdStockCount = $createdStockCount->toArray();
        $this->assertArrayHasKey('id', $createdStockCount);
        $this->assertNotNull($createdStockCount['id'], 'Created StockCount must have id specified');
        $this->assertNotNull(StockCount::find($createdStockCount['id']), 'StockCount with given id must be in DB');
        $this->assertModelData($stockCount, $createdStockCount);
    }

    /**
     * @test read
     */
    public function test_read_stock_count()
    {
        $stockCount = factory(StockCount::class)->create();

        $dbStockCount = $this->stockCountRepo->find($stockCount->id);

        $dbStockCount = $dbStockCount->toArray();
        $this->assertModelData($stockCount->toArray(), $dbStockCount);
    }

    /**
     * @test update
     */
    public function test_update_stock_count()
    {
        $stockCount = factory(StockCount::class)->create();
        $fakeStockCount = factory(StockCount::class)->make()->toArray();

        $updatedStockCount = $this->stockCountRepo->update($fakeStockCount, $stockCount->id);

        $this->assertModelData($fakeStockCount, $updatedStockCount->toArray());
        $dbStockCount = $this->stockCountRepo->find($stockCount->id);
        $this->assertModelData($fakeStockCount, $dbStockCount->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_stock_count()
    {
        $stockCount = factory(StockCount::class)->create();

        $resp = $this->stockCountRepo->delete($stockCount->id);

        $this->assertTrue($resp);
        $this->assertNull(StockCount::find($stockCount->id), 'StockCount should not exist in DB');
    }
}
