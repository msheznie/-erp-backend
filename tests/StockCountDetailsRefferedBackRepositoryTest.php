<?php namespace Tests\Repositories;

use App\Models\StockCountDetailsRefferedBack;
use App\Repositories\StockCountDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class StockCountDetailsRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockCountDetailsRefferedBackRepository
     */
    protected $stockCountDetailsRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->stockCountDetailsRefferedBackRepo = \App::make(StockCountDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->make()->toArray();

        $createdStockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepo->create($stockCountDetailsRefferedBack);

        $createdStockCountDetailsRefferedBack = $createdStockCountDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdStockCountDetailsRefferedBack);
        $this->assertNotNull($createdStockCountDetailsRefferedBack['id'], 'Created StockCountDetailsRefferedBack must have id specified');
        $this->assertNotNull(StockCountDetailsRefferedBack::find($createdStockCountDetailsRefferedBack['id']), 'StockCountDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($stockCountDetailsRefferedBack, $createdStockCountDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->create();

        $dbStockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepo->find($stockCountDetailsRefferedBack->id);

        $dbStockCountDetailsRefferedBack = $dbStockCountDetailsRefferedBack->toArray();
        $this->assertModelData($stockCountDetailsRefferedBack->toArray(), $dbStockCountDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->create();
        $fakeStockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->make()->toArray();

        $updatedStockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepo->update($fakeStockCountDetailsRefferedBack, $stockCountDetailsRefferedBack->id);

        $this->assertModelData($fakeStockCountDetailsRefferedBack, $updatedStockCountDetailsRefferedBack->toArray());
        $dbStockCountDetailsRefferedBack = $this->stockCountDetailsRefferedBackRepo->find($stockCountDetailsRefferedBack->id);
        $this->assertModelData($fakeStockCountDetailsRefferedBack, $dbStockCountDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->create();

        $resp = $this->stockCountDetailsRefferedBackRepo->delete($stockCountDetailsRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(StockCountDetailsRefferedBack::find($stockCountDetailsRefferedBack->id), 'StockCountDetailsRefferedBack should not exist in DB');
    }
}
