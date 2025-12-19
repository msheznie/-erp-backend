<?php

use App\Models\PurchaseRequestDetails;
use App\Repositories\PurchaseRequestDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRequestDetailsRepositoryTest extends TestCase
{
    use MakePurchaseRequestDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseRequestDetailsRepository
     */
    protected $purchaseRequestDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseRequestDetailsRepo = App::make(PurchaseRequestDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->fakePurchaseRequestDetailsData();
        $createdPurchaseRequestDetails = $this->purchaseRequestDetailsRepo->create($purchaseRequestDetails);
        $createdPurchaseRequestDetails = $createdPurchaseRequestDetails->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseRequestDetails);
        $this->assertNotNull($createdPurchaseRequestDetails['id'], 'Created PurchaseRequestDetails must have id specified');
        $this->assertNotNull(PurchaseRequestDetails::find($createdPurchaseRequestDetails['id']), 'PurchaseRequestDetails with given id must be in DB');
        $this->assertModelData($purchaseRequestDetails, $createdPurchaseRequestDetails);
    }

    /**
     * @test read
     */
    public function testReadPurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->makePurchaseRequestDetails();
        $dbPurchaseRequestDetails = $this->purchaseRequestDetailsRepo->find($purchaseRequestDetails->id);
        $dbPurchaseRequestDetails = $dbPurchaseRequestDetails->toArray();
        $this->assertModelData($purchaseRequestDetails->toArray(), $dbPurchaseRequestDetails);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->makePurchaseRequestDetails();
        $fakePurchaseRequestDetails = $this->fakePurchaseRequestDetailsData();
        $updatedPurchaseRequestDetails = $this->purchaseRequestDetailsRepo->update($fakePurchaseRequestDetails, $purchaseRequestDetails->id);
        $this->assertModelData($fakePurchaseRequestDetails, $updatedPurchaseRequestDetails->toArray());
        $dbPurchaseRequestDetails = $this->purchaseRequestDetailsRepo->find($purchaseRequestDetails->id);
        $this->assertModelData($fakePurchaseRequestDetails, $dbPurchaseRequestDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->makePurchaseRequestDetails();
        $resp = $this->purchaseRequestDetailsRepo->delete($purchaseRequestDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseRequestDetails::find($purchaseRequestDetails->id), 'PurchaseRequestDetails should not exist in DB');
    }
}
