<?php

use App\Models\PurchaseOrderStatus;
use App\Repositories\PurchaseOrderStatusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderStatusRepositoryTest extends TestCase
{
    use MakePurchaseOrderStatusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderStatusRepository
     */
    protected $purchaseOrderStatusRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderStatusRepo = App::make(PurchaseOrderStatusRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->fakePurchaseOrderStatusData();
        $createdPurchaseOrderStatus = $this->purchaseOrderStatusRepo->create($purchaseOrderStatus);
        $createdPurchaseOrderStatus = $createdPurchaseOrderStatus->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderStatus);
        $this->assertNotNull($createdPurchaseOrderStatus['id'], 'Created PurchaseOrderStatus must have id specified');
        $this->assertNotNull(PurchaseOrderStatus::find($createdPurchaseOrderStatus['id']), 'PurchaseOrderStatus with given id must be in DB');
        $this->assertModelData($purchaseOrderStatus, $createdPurchaseOrderStatus);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->makePurchaseOrderStatus();
        $dbPurchaseOrderStatus = $this->purchaseOrderStatusRepo->find($purchaseOrderStatus->id);
        $dbPurchaseOrderStatus = $dbPurchaseOrderStatus->toArray();
        $this->assertModelData($purchaseOrderStatus->toArray(), $dbPurchaseOrderStatus);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->makePurchaseOrderStatus();
        $fakePurchaseOrderStatus = $this->fakePurchaseOrderStatusData();
        $updatedPurchaseOrderStatus = $this->purchaseOrderStatusRepo->update($fakePurchaseOrderStatus, $purchaseOrderStatus->id);
        $this->assertModelData($fakePurchaseOrderStatus, $updatedPurchaseOrderStatus->toArray());
        $dbPurchaseOrderStatus = $this->purchaseOrderStatusRepo->find($purchaseOrderStatus->id);
        $this->assertModelData($fakePurchaseOrderStatus, $dbPurchaseOrderStatus->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->makePurchaseOrderStatus();
        $resp = $this->purchaseOrderStatusRepo->delete($purchaseOrderStatus->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderStatus::find($purchaseOrderStatus->id), 'PurchaseOrderStatus should not exist in DB');
    }
}
