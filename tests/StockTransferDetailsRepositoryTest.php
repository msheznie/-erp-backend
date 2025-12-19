<?php

use App\Models\StockTransferDetails;
use App\Repositories\StockTransferDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferDetailsRepositoryTest extends TestCase
{
    use MakeStockTransferDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockTransferDetailsRepository
     */
    protected $stockTransferDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockTransferDetailsRepo = App::make(StockTransferDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockTransferDetails()
    {
        $stockTransferDetails = $this->fakeStockTransferDetailsData();
        $createdStockTransferDetails = $this->stockTransferDetailsRepo->create($stockTransferDetails);
        $createdStockTransferDetails = $createdStockTransferDetails->toArray();
        $this->assertArrayHasKey('id', $createdStockTransferDetails);
        $this->assertNotNull($createdStockTransferDetails['id'], 'Created StockTransferDetails must have id specified');
        $this->assertNotNull(StockTransferDetails::find($createdStockTransferDetails['id']), 'StockTransferDetails with given id must be in DB');
        $this->assertModelData($stockTransferDetails, $createdStockTransferDetails);
    }

    /**
     * @test read
     */
    public function testReadStockTransferDetails()
    {
        $stockTransferDetails = $this->makeStockTransferDetails();
        $dbStockTransferDetails = $this->stockTransferDetailsRepo->find($stockTransferDetails->id);
        $dbStockTransferDetails = $dbStockTransferDetails->toArray();
        $this->assertModelData($stockTransferDetails->toArray(), $dbStockTransferDetails);
    }

    /**
     * @test update
     */
    public function testUpdateStockTransferDetails()
    {
        $stockTransferDetails = $this->makeStockTransferDetails();
        $fakeStockTransferDetails = $this->fakeStockTransferDetailsData();
        $updatedStockTransferDetails = $this->stockTransferDetailsRepo->update($fakeStockTransferDetails, $stockTransferDetails->id);
        $this->assertModelData($fakeStockTransferDetails, $updatedStockTransferDetails->toArray());
        $dbStockTransferDetails = $this->stockTransferDetailsRepo->find($stockTransferDetails->id);
        $this->assertModelData($fakeStockTransferDetails, $dbStockTransferDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockTransferDetails()
    {
        $stockTransferDetails = $this->makeStockTransferDetails();
        $resp = $this->stockTransferDetailsRepo->delete($stockTransferDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(StockTransferDetails::find($stockTransferDetails->id), 'StockTransferDetails should not exist in DB');
    }
}
