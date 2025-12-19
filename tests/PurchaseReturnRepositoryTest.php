<?php

use App\Models\PurchaseReturn;
use App\Repositories\PurchaseReturnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseReturnRepositoryTest extends TestCase
{
    use MakePurchaseReturnTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseReturnRepository
     */
    protected $purchaseReturnRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseReturnRepo = App::make(PurchaseReturnRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseReturn()
    {
        $purchaseReturn = $this->fakePurchaseReturnData();
        $createdPurchaseReturn = $this->purchaseReturnRepo->create($purchaseReturn);
        $createdPurchaseReturn = $createdPurchaseReturn->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseReturn);
        $this->assertNotNull($createdPurchaseReturn['id'], 'Created PurchaseReturn must have id specified');
        $this->assertNotNull(PurchaseReturn::find($createdPurchaseReturn['id']), 'PurchaseReturn with given id must be in DB');
        $this->assertModelData($purchaseReturn, $createdPurchaseReturn);
    }

    /**
     * @test read
     */
    public function testReadPurchaseReturn()
    {
        $purchaseReturn = $this->makePurchaseReturn();
        $dbPurchaseReturn = $this->purchaseReturnRepo->find($purchaseReturn->id);
        $dbPurchaseReturn = $dbPurchaseReturn->toArray();
        $this->assertModelData($purchaseReturn->toArray(), $dbPurchaseReturn);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseReturn()
    {
        $purchaseReturn = $this->makePurchaseReturn();
        $fakePurchaseReturn = $this->fakePurchaseReturnData();
        $updatedPurchaseReturn = $this->purchaseReturnRepo->update($fakePurchaseReturn, $purchaseReturn->id);
        $this->assertModelData($fakePurchaseReturn, $updatedPurchaseReturn->toArray());
        $dbPurchaseReturn = $this->purchaseReturnRepo->find($purchaseReturn->id);
        $this->assertModelData($fakePurchaseReturn, $dbPurchaseReturn->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseReturn()
    {
        $purchaseReturn = $this->makePurchaseReturn();
        $resp = $this->purchaseReturnRepo->delete($purchaseReturn->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseReturn::find($purchaseReturn->id), 'PurchaseReturn should not exist in DB');
    }
}
