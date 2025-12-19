<?php

use App\Models\PurchaseOrderDetails;
use App\Repositories\PurchaseOrderDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderDetailsRepositoryTest extends TestCase
{
    use MakePurchaseOrderDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderDetailsRepository
     */
    protected $purchaseOrderDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderDetailsRepo = App::make(PurchaseOrderDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->fakePurchaseOrderDetailsData();
        $createdPurchaseOrderDetails = $this->purchaseOrderDetailsRepo->create($purchaseOrderDetails);
        $createdPurchaseOrderDetails = $createdPurchaseOrderDetails->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderDetails);
        $this->assertNotNull($createdPurchaseOrderDetails['id'], 'Created PurchaseOrderDetails must have id specified');
        $this->assertNotNull(PurchaseOrderDetails::find($createdPurchaseOrderDetails['id']), 'PurchaseOrderDetails with given id must be in DB');
        $this->assertModelData($purchaseOrderDetails, $createdPurchaseOrderDetails);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->makePurchaseOrderDetails();
        $dbPurchaseOrderDetails = $this->purchaseOrderDetailsRepo->find($purchaseOrderDetails->id);
        $dbPurchaseOrderDetails = $dbPurchaseOrderDetails->toArray();
        $this->assertModelData($purchaseOrderDetails->toArray(), $dbPurchaseOrderDetails);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->makePurchaseOrderDetails();
        $fakePurchaseOrderDetails = $this->fakePurchaseOrderDetailsData();
        $updatedPurchaseOrderDetails = $this->purchaseOrderDetailsRepo->update($fakePurchaseOrderDetails, $purchaseOrderDetails->id);
        $this->assertModelData($fakePurchaseOrderDetails, $updatedPurchaseOrderDetails->toArray());
        $dbPurchaseOrderDetails = $this->purchaseOrderDetailsRepo->find($purchaseOrderDetails->id);
        $this->assertModelData($fakePurchaseOrderDetails, $dbPurchaseOrderDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->makePurchaseOrderDetails();
        $resp = $this->purchaseOrderDetailsRepo->delete($purchaseOrderDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderDetails::find($purchaseOrderDetails->id), 'PurchaseOrderDetails should not exist in DB');
    }
}
