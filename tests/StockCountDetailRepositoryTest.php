<?php namespace Tests\Repositories;

use App\Models\StockCountDetail;
use App\Repositories\StockCountDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class StockCountDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockCountDetailRepository
     */
    protected $stockCountDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->stockCountDetailRepo = \App::make(StockCountDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->make()->toArray();

        $createdStockCountDetail = $this->stockCountDetailRepo->create($stockCountDetail);

        $createdStockCountDetail = $createdStockCountDetail->toArray();
        $this->assertArrayHasKey('id', $createdStockCountDetail);
        $this->assertNotNull($createdStockCountDetail['id'], 'Created StockCountDetail must have id specified');
        $this->assertNotNull(StockCountDetail::find($createdStockCountDetail['id']), 'StockCountDetail with given id must be in DB');
        $this->assertModelData($stockCountDetail, $createdStockCountDetail);
    }

    /**
     * @test read
     */
    public function test_read_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->create();

        $dbStockCountDetail = $this->stockCountDetailRepo->find($stockCountDetail->id);

        $dbStockCountDetail = $dbStockCountDetail->toArray();
        $this->assertModelData($stockCountDetail->toArray(), $dbStockCountDetail);
    }

    /**
     * @test update
     */
    public function test_update_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->create();
        $fakeStockCountDetail = factory(StockCountDetail::class)->make()->toArray();

        $updatedStockCountDetail = $this->stockCountDetailRepo->update($fakeStockCountDetail, $stockCountDetail->id);

        $this->assertModelData($fakeStockCountDetail, $updatedStockCountDetail->toArray());
        $dbStockCountDetail = $this->stockCountDetailRepo->find($stockCountDetail->id);
        $this->assertModelData($fakeStockCountDetail, $dbStockCountDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->create();

        $resp = $this->stockCountDetailRepo->delete($stockCountDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(StockCountDetail::find($stockCountDetail->id), 'StockCountDetail should not exist in DB');
    }
}
