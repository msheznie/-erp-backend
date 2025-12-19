<?php

use App\Models\PurchaseReturnDetails;
use App\Repositories\PurchaseReturnDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseReturnDetailsRepositoryTest extends TestCase
{
    use MakePurchaseReturnDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseReturnDetailsRepository
     */
    protected $purchaseReturnDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseReturnDetailsRepo = App::make(PurchaseReturnDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->fakePurchaseReturnDetailsData();
        $createdPurchaseReturnDetails = $this->purchaseReturnDetailsRepo->create($purchaseReturnDetails);
        $createdPurchaseReturnDetails = $createdPurchaseReturnDetails->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseReturnDetails);
        $this->assertNotNull($createdPurchaseReturnDetails['id'], 'Created PurchaseReturnDetails must have id specified');
        $this->assertNotNull(PurchaseReturnDetails::find($createdPurchaseReturnDetails['id']), 'PurchaseReturnDetails with given id must be in DB');
        $this->assertModelData($purchaseReturnDetails, $createdPurchaseReturnDetails);
    }

    /**
     * @test read
     */
    public function testReadPurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->makePurchaseReturnDetails();
        $dbPurchaseReturnDetails = $this->purchaseReturnDetailsRepo->find($purchaseReturnDetails->id);
        $dbPurchaseReturnDetails = $dbPurchaseReturnDetails->toArray();
        $this->assertModelData($purchaseReturnDetails->toArray(), $dbPurchaseReturnDetails);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->makePurchaseReturnDetails();
        $fakePurchaseReturnDetails = $this->fakePurchaseReturnDetailsData();
        $updatedPurchaseReturnDetails = $this->purchaseReturnDetailsRepo->update($fakePurchaseReturnDetails, $purchaseReturnDetails->id);
        $this->assertModelData($fakePurchaseReturnDetails, $updatedPurchaseReturnDetails->toArray());
        $dbPurchaseReturnDetails = $this->purchaseReturnDetailsRepo->find($purchaseReturnDetails->id);
        $this->assertModelData($fakePurchaseReturnDetails, $dbPurchaseReturnDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->makePurchaseReturnDetails();
        $resp = $this->purchaseReturnDetailsRepo->delete($purchaseReturnDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseReturnDetails::find($purchaseReturnDetails->id), 'PurchaseReturnDetails should not exist in DB');
    }
}
