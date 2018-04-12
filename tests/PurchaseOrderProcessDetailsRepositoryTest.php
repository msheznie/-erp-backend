<?php

use App\Models\PurchaseOrderProcessDetails;
use App\Repositories\PurchaseOrderProcessDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderProcessDetailsRepositoryTest extends TestCase
{
    use MakePurchaseOrderProcessDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderProcessDetailsRepository
     */
    protected $purchaseOrderProcessDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderProcessDetailsRepo = App::make(PurchaseOrderProcessDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->fakePurchaseOrderProcessDetailsData();
        $createdPurchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepo->create($purchaseOrderProcessDetails);
        $createdPurchaseOrderProcessDetails = $createdPurchaseOrderProcessDetails->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderProcessDetails);
        $this->assertNotNull($createdPurchaseOrderProcessDetails['id'], 'Created PurchaseOrderProcessDetails must have id specified');
        $this->assertNotNull(PurchaseOrderProcessDetails::find($createdPurchaseOrderProcessDetails['id']), 'PurchaseOrderProcessDetails with given id must be in DB');
        $this->assertModelData($purchaseOrderProcessDetails, $createdPurchaseOrderProcessDetails);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->makePurchaseOrderProcessDetails();
        $dbPurchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepo->find($purchaseOrderProcessDetails->id);
        $dbPurchaseOrderProcessDetails = $dbPurchaseOrderProcessDetails->toArray();
        $this->assertModelData($purchaseOrderProcessDetails->toArray(), $dbPurchaseOrderProcessDetails);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->makePurchaseOrderProcessDetails();
        $fakePurchaseOrderProcessDetails = $this->fakePurchaseOrderProcessDetailsData();
        $updatedPurchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepo->update($fakePurchaseOrderProcessDetails, $purchaseOrderProcessDetails->id);
        $this->assertModelData($fakePurchaseOrderProcessDetails, $updatedPurchaseOrderProcessDetails->toArray());
        $dbPurchaseOrderProcessDetails = $this->purchaseOrderProcessDetailsRepo->find($purchaseOrderProcessDetails->id);
        $this->assertModelData($fakePurchaseOrderProcessDetails, $dbPurchaseOrderProcessDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->makePurchaseOrderProcessDetails();
        $resp = $this->purchaseOrderProcessDetailsRepo->delete($purchaseOrderProcessDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderProcessDetails::find($purchaseOrderProcessDetails->id), 'PurchaseOrderProcessDetails should not exist in DB');
    }
}
