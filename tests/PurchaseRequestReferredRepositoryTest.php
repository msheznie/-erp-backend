<?php

use App\Models\PurchaseRequestReferred;
use App\Repositories\PurchaseRequestReferredRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRequestReferredRepositoryTest extends TestCase
{
    use MakePurchaseRequestReferredTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseRequestReferredRepository
     */
    protected $purchaseRequestReferredRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseRequestReferredRepo = App::make(PurchaseRequestReferredRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->fakePurchaseRequestReferredData();
        $createdPurchaseRequestReferred = $this->purchaseRequestReferredRepo->create($purchaseRequestReferred);
        $createdPurchaseRequestReferred = $createdPurchaseRequestReferred->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseRequestReferred);
        $this->assertNotNull($createdPurchaseRequestReferred['id'], 'Created PurchaseRequestReferred must have id specified');
        $this->assertNotNull(PurchaseRequestReferred::find($createdPurchaseRequestReferred['id']), 'PurchaseRequestReferred with given id must be in DB');
        $this->assertModelData($purchaseRequestReferred, $createdPurchaseRequestReferred);
    }

    /**
     * @test read
     */
    public function testReadPurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->makePurchaseRequestReferred();
        $dbPurchaseRequestReferred = $this->purchaseRequestReferredRepo->find($purchaseRequestReferred->id);
        $dbPurchaseRequestReferred = $dbPurchaseRequestReferred->toArray();
        $this->assertModelData($purchaseRequestReferred->toArray(), $dbPurchaseRequestReferred);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->makePurchaseRequestReferred();
        $fakePurchaseRequestReferred = $this->fakePurchaseRequestReferredData();
        $updatedPurchaseRequestReferred = $this->purchaseRequestReferredRepo->update($fakePurchaseRequestReferred, $purchaseRequestReferred->id);
        $this->assertModelData($fakePurchaseRequestReferred, $updatedPurchaseRequestReferred->toArray());
        $dbPurchaseRequestReferred = $this->purchaseRequestReferredRepo->find($purchaseRequestReferred->id);
        $this->assertModelData($fakePurchaseRequestReferred, $dbPurchaseRequestReferred->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->makePurchaseRequestReferred();
        $resp = $this->purchaseRequestReferredRepo->delete($purchaseRequestReferred->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseRequestReferred::find($purchaseRequestReferred->id), 'PurchaseRequestReferred should not exist in DB');
    }
}
