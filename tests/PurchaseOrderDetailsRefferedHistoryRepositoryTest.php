<?php

use App\Models\PurchaseOrderDetailsRefferedHistory;
use App\Repositories\PurchaseOrderDetailsRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderDetailsRefferedHistoryRepositoryTest extends TestCase
{
    use MakePurchaseOrderDetailsRefferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderDetailsRefferedHistoryRepository
     */
    protected $purchaseOrderDetailsRefferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderDetailsRefferedHistoryRepo = App::make(PurchaseOrderDetailsRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->fakePurchaseOrderDetailsRefferedHistoryData();
        $createdPurchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepo->create($purchaseOrderDetailsRefferedHistory);
        $createdPurchaseOrderDetailsRefferedHistory = $createdPurchaseOrderDetailsRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderDetailsRefferedHistory);
        $this->assertNotNull($createdPurchaseOrderDetailsRefferedHistory['id'], 'Created PurchaseOrderDetailsRefferedHistory must have id specified');
        $this->assertNotNull(PurchaseOrderDetailsRefferedHistory::find($createdPurchaseOrderDetailsRefferedHistory['id']), 'PurchaseOrderDetailsRefferedHistory with given id must be in DB');
        $this->assertModelData($purchaseOrderDetailsRefferedHistory, $createdPurchaseOrderDetailsRefferedHistory);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->makePurchaseOrderDetailsRefferedHistory();
        $dbPurchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepo->find($purchaseOrderDetailsRefferedHistory->id);
        $dbPurchaseOrderDetailsRefferedHistory = $dbPurchaseOrderDetailsRefferedHistory->toArray();
        $this->assertModelData($purchaseOrderDetailsRefferedHistory->toArray(), $dbPurchaseOrderDetailsRefferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->makePurchaseOrderDetailsRefferedHistory();
        $fakePurchaseOrderDetailsRefferedHistory = $this->fakePurchaseOrderDetailsRefferedHistoryData();
        $updatedPurchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepo->update($fakePurchaseOrderDetailsRefferedHistory, $purchaseOrderDetailsRefferedHistory->id);
        $this->assertModelData($fakePurchaseOrderDetailsRefferedHistory, $updatedPurchaseOrderDetailsRefferedHistory->toArray());
        $dbPurchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepo->find($purchaseOrderDetailsRefferedHistory->id);
        $this->assertModelData($fakePurchaseOrderDetailsRefferedHistory, $dbPurchaseOrderDetailsRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->makePurchaseOrderDetailsRefferedHistory();
        $resp = $this->purchaseOrderDetailsRefferedHistoryRepo->delete($purchaseOrderDetailsRefferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderDetailsRefferedHistory::find($purchaseOrderDetailsRefferedHistory->id), 'PurchaseOrderDetailsRefferedHistory should not exist in DB');
    }
}
