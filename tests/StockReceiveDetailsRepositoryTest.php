<?php

use App\Models\StockReceiveDetails;
use App\Repositories\StockReceiveDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveDetailsRepositoryTest extends TestCase
{
    use MakeStockReceiveDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var StockReceiveDetailsRepository
     */
    protected $stockReceiveDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->stockReceiveDetailsRepo = App::make(StockReceiveDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateStockReceiveDetails()
    {
        $stockReceiveDetails = $this->fakeStockReceiveDetailsData();
        $createdStockReceiveDetails = $this->stockReceiveDetailsRepo->create($stockReceiveDetails);
        $createdStockReceiveDetails = $createdStockReceiveDetails->toArray();
        $this->assertArrayHasKey('id', $createdStockReceiveDetails);
        $this->assertNotNull($createdStockReceiveDetails['id'], 'Created StockReceiveDetails must have id specified');
        $this->assertNotNull(StockReceiveDetails::find($createdStockReceiveDetails['id']), 'StockReceiveDetails with given id must be in DB');
        $this->assertModelData($stockReceiveDetails, $createdStockReceiveDetails);
    }

    /**
     * @test read
     */
    public function testReadStockReceiveDetails()
    {
        $stockReceiveDetails = $this->makeStockReceiveDetails();
        $dbStockReceiveDetails = $this->stockReceiveDetailsRepo->find($stockReceiveDetails->id);
        $dbStockReceiveDetails = $dbStockReceiveDetails->toArray();
        $this->assertModelData($stockReceiveDetails->toArray(), $dbStockReceiveDetails);
    }

    /**
     * @test update
     */
    public function testUpdateStockReceiveDetails()
    {
        $stockReceiveDetails = $this->makeStockReceiveDetails();
        $fakeStockReceiveDetails = $this->fakeStockReceiveDetailsData();
        $updatedStockReceiveDetails = $this->stockReceiveDetailsRepo->update($fakeStockReceiveDetails, $stockReceiveDetails->id);
        $this->assertModelData($fakeStockReceiveDetails, $updatedStockReceiveDetails->toArray());
        $dbStockReceiveDetails = $this->stockReceiveDetailsRepo->find($stockReceiveDetails->id);
        $this->assertModelData($fakeStockReceiveDetails, $dbStockReceiveDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteStockReceiveDetails()
    {
        $stockReceiveDetails = $this->makeStockReceiveDetails();
        $resp = $this->stockReceiveDetailsRepo->delete($stockReceiveDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(StockReceiveDetails::find($stockReceiveDetails->id), 'StockReceiveDetails should not exist in DB');
    }
}
