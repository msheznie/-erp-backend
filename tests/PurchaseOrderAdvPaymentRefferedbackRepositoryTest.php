<?php

use App\Models\PurchaseOrderAdvPaymentRefferedback;
use App\Repositories\PurchaseOrderAdvPaymentRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderAdvPaymentRefferedbackRepositoryTest extends TestCase
{
    use MakePurchaseOrderAdvPaymentRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderAdvPaymentRefferedbackRepository
     */
    protected $purchaseOrderAdvPaymentRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderAdvPaymentRefferedbackRepo = App::make(PurchaseOrderAdvPaymentRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->fakePurchaseOrderAdvPaymentRefferedbackData();
        $createdPurchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepo->create($purchaseOrderAdvPaymentRefferedback);
        $createdPurchaseOrderAdvPaymentRefferedback = $createdPurchaseOrderAdvPaymentRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderAdvPaymentRefferedback);
        $this->assertNotNull($createdPurchaseOrderAdvPaymentRefferedback['id'], 'Created PurchaseOrderAdvPaymentRefferedback must have id specified');
        $this->assertNotNull(PurchaseOrderAdvPaymentRefferedback::find($createdPurchaseOrderAdvPaymentRefferedback['id']), 'PurchaseOrderAdvPaymentRefferedback with given id must be in DB');
        $this->assertModelData($purchaseOrderAdvPaymentRefferedback, $createdPurchaseOrderAdvPaymentRefferedback);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->makePurchaseOrderAdvPaymentRefferedback();
        $dbPurchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepo->find($purchaseOrderAdvPaymentRefferedback->id);
        $dbPurchaseOrderAdvPaymentRefferedback = $dbPurchaseOrderAdvPaymentRefferedback->toArray();
        $this->assertModelData($purchaseOrderAdvPaymentRefferedback->toArray(), $dbPurchaseOrderAdvPaymentRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->makePurchaseOrderAdvPaymentRefferedback();
        $fakePurchaseOrderAdvPaymentRefferedback = $this->fakePurchaseOrderAdvPaymentRefferedbackData();
        $updatedPurchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepo->update($fakePurchaseOrderAdvPaymentRefferedback, $purchaseOrderAdvPaymentRefferedback->id);
        $this->assertModelData($fakePurchaseOrderAdvPaymentRefferedback, $updatedPurchaseOrderAdvPaymentRefferedback->toArray());
        $dbPurchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepo->find($purchaseOrderAdvPaymentRefferedback->id);
        $this->assertModelData($fakePurchaseOrderAdvPaymentRefferedback, $dbPurchaseOrderAdvPaymentRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->makePurchaseOrderAdvPaymentRefferedback();
        $resp = $this->purchaseOrderAdvPaymentRefferedbackRepo->delete($purchaseOrderAdvPaymentRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderAdvPaymentRefferedback::find($purchaseOrderAdvPaymentRefferedback->id), 'PurchaseOrderAdvPaymentRefferedback should not exist in DB');
    }
}
