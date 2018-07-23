<?php

use App\Models\PurchaseOrderMasterRefferedHistory;
use App\Repositories\PurchaseOrderMasterRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderMasterRefferedHistoryRepositoryTest extends TestCase
{
    use MakePurchaseOrderMasterRefferedHistoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderMasterRefferedHistoryRepository
     */
    protected $purchaseOrderMasterRefferedHistoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderMasterRefferedHistoryRepo = App::make(PurchaseOrderMasterRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->fakePurchaseOrderMasterRefferedHistoryData();
        $createdPurchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepo->create($purchaseOrderMasterRefferedHistory);
        $createdPurchaseOrderMasterRefferedHistory = $createdPurchaseOrderMasterRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderMasterRefferedHistory);
        $this->assertNotNull($createdPurchaseOrderMasterRefferedHistory['id'], 'Created PurchaseOrderMasterRefferedHistory must have id specified');
        $this->assertNotNull(PurchaseOrderMasterRefferedHistory::find($createdPurchaseOrderMasterRefferedHistory['id']), 'PurchaseOrderMasterRefferedHistory with given id must be in DB');
        $this->assertModelData($purchaseOrderMasterRefferedHistory, $createdPurchaseOrderMasterRefferedHistory);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->makePurchaseOrderMasterRefferedHistory();
        $dbPurchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepo->find($purchaseOrderMasterRefferedHistory->id);
        $dbPurchaseOrderMasterRefferedHistory = $dbPurchaseOrderMasterRefferedHistory->toArray();
        $this->assertModelData($purchaseOrderMasterRefferedHistory->toArray(), $dbPurchaseOrderMasterRefferedHistory);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->makePurchaseOrderMasterRefferedHistory();
        $fakePurchaseOrderMasterRefferedHistory = $this->fakePurchaseOrderMasterRefferedHistoryData();
        $updatedPurchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepo->update($fakePurchaseOrderMasterRefferedHistory, $purchaseOrderMasterRefferedHistory->id);
        $this->assertModelData($fakePurchaseOrderMasterRefferedHistory, $updatedPurchaseOrderMasterRefferedHistory->toArray());
        $dbPurchaseOrderMasterRefferedHistory = $this->purchaseOrderMasterRefferedHistoryRepo->find($purchaseOrderMasterRefferedHistory->id);
        $this->assertModelData($fakePurchaseOrderMasterRefferedHistory, $dbPurchaseOrderMasterRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->makePurchaseOrderMasterRefferedHistory();
        $resp = $this->purchaseOrderMasterRefferedHistoryRepo->delete($purchaseOrderMasterRefferedHistory->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderMasterRefferedHistory::find($purchaseOrderMasterRefferedHistory->id), 'PurchaseOrderMasterRefferedHistory should not exist in DB');
    }
}
