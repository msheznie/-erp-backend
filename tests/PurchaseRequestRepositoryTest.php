<?php

use App\Models\PurchaseRequest;
use App\Repositories\PurchaseRequestRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRequestRepositoryTest extends TestCase
{
    use MakePurchaseRequestTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseRequestRepository
     */
    protected $purchaseRequestRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseRequestRepo = App::make(PurchaseRequestRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseRequest()
    {
        $purchaseRequest = $this->fakePurchaseRequestData();
        $createdPurchaseRequest = $this->purchaseRequestRepo->create($purchaseRequest);
        $createdPurchaseRequest = $createdPurchaseRequest->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseRequest);
        $this->assertNotNull($createdPurchaseRequest['id'], 'Created PurchaseRequest must have id specified');
        $this->assertNotNull(PurchaseRequest::find($createdPurchaseRequest['id']), 'PurchaseRequest with given id must be in DB');
        $this->assertModelData($purchaseRequest, $createdPurchaseRequest);
    }

    /**
     * @test read
     */
    public function testReadPurchaseRequest()
    {
        $purchaseRequest = $this->makePurchaseRequest();
        $dbPurchaseRequest = $this->purchaseRequestRepo->find($purchaseRequest->id);
        $dbPurchaseRequest = $dbPurchaseRequest->toArray();
        $this->assertModelData($purchaseRequest->toArray(), $dbPurchaseRequest);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseRequest()
    {
        $purchaseRequest = $this->makePurchaseRequest();
        $fakePurchaseRequest = $this->fakePurchaseRequestData();
        $updatedPurchaseRequest = $this->purchaseRequestRepo->update($fakePurchaseRequest, $purchaseRequest->id);
        $this->assertModelData($fakePurchaseRequest, $updatedPurchaseRequest->toArray());
        $dbPurchaseRequest = $this->purchaseRequestRepo->find($purchaseRequest->id);
        $this->assertModelData($fakePurchaseRequest, $dbPurchaseRequest->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseRequest()
    {
        $purchaseRequest = $this->makePurchaseRequest();
        $resp = $this->purchaseRequestRepo->delete($purchaseRequest->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseRequest::find($purchaseRequest->id), 'PurchaseRequest should not exist in DB');
    }
}
