<?php

use App\Models\StockAdjustmentDetails;
use App\Repositories\StockAdjustmentDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentDetailsRepositoryTest extends TestCase
{
    use MakeStockAdjustmentDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockAdjustmentDetailsRepository
     */
    protected $stockAdjustmentDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockAdjustmentDetailsRepo = App::make(StockAdjustmentDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->fakeStockAdjustmentDetailsData();
        $createdStockAdjustmentDetails = $this->stockAdjustmentDetailsRepo->create($stockAdjustmentDetails);
        $createdStockAdjustmentDetails = $createdStockAdjustmentDetails->toArray();
        $this->assertArrayHasKey('id', $createdStockAdjustmentDetails);
        $this->assertNotNull($createdStockAdjustmentDetails['id'], 'Created StockAdjustmentDetails must have id specified');
        $this->assertNotNull(StockAdjustmentDetails::find($createdStockAdjustmentDetails['id']), 'StockAdjustmentDetails with given id must be in DB');
        $this->assertModelData($stockAdjustmentDetails, $createdStockAdjustmentDetails);
    }

    /**
     * @test read
     */
    public function testReadStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->makeStockAdjustmentDetails();
        $dbStockAdjustmentDetails = $this->stockAdjustmentDetailsRepo->find($stockAdjustmentDetails->id);
        $dbStockAdjustmentDetails = $dbStockAdjustmentDetails->toArray();
        $this->assertModelData($stockAdjustmentDetails->toArray(), $dbStockAdjustmentDetails);
    }

    /**
     * @test update
     */
    public function testUpdateStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->makeStockAdjustmentDetails();
        $fakeStockAdjustmentDetails = $this->fakeStockAdjustmentDetailsData();
        $updatedStockAdjustmentDetails = $this->stockAdjustmentDetailsRepo->update($fakeStockAdjustmentDetails, $stockAdjustmentDetails->id);
        $this->assertModelData($fakeStockAdjustmentDetails, $updatedStockAdjustmentDetails->toArray());
        $dbStockAdjustmentDetails = $this->stockAdjustmentDetailsRepo->find($stockAdjustmentDetails->id);
        $this->assertModelData($fakeStockAdjustmentDetails, $dbStockAdjustmentDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockAdjustmentDetails()
    {
        $stockAdjustmentDetails = $this->makeStockAdjustmentDetails();
        $resp = $this->stockAdjustmentDetailsRepo->delete($stockAdjustmentDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(StockAdjustmentDetails::find($stockAdjustmentDetails->id), 'StockAdjustmentDetails should not exist in DB');
    }
}
