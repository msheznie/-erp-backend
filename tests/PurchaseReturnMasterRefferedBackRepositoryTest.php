<?php namespace Tests\Repositories;

use App\Models\PurchaseReturnMasterRefferedBack;
use App\Repositories\PurchaseReturnMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PurchaseReturnMasterRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseReturnMasterRefferedBackRepository
     */
    protected $purchaseReturnMasterRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->purchaseReturnMasterRefferedBackRepo = \App::make(PurchaseReturnMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->make()->toArray();

        $createdPurchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepo->create($purchaseReturnMasterRefferedBack);

        $createdPurchaseReturnMasterRefferedBack = $createdPurchaseReturnMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseReturnMasterRefferedBack);
        $this->assertNotNull($createdPurchaseReturnMasterRefferedBack['id'], 'Created PurchaseReturnMasterRefferedBack must have id specified');
        $this->assertNotNull(PurchaseReturnMasterRefferedBack::find($createdPurchaseReturnMasterRefferedBack['id']), 'PurchaseReturnMasterRefferedBack with given id must be in DB');
        $this->assertModelData($purchaseReturnMasterRefferedBack, $createdPurchaseReturnMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->create();

        $dbPurchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepo->find($purchaseReturnMasterRefferedBack->id);

        $dbPurchaseReturnMasterRefferedBack = $dbPurchaseReturnMasterRefferedBack->toArray();
        $this->assertModelData($purchaseReturnMasterRefferedBack->toArray(), $dbPurchaseReturnMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->create();
        $fakePurchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->make()->toArray();

        $updatedPurchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepo->update($fakePurchaseReturnMasterRefferedBack, $purchaseReturnMasterRefferedBack->id);

        $this->assertModelData($fakePurchaseReturnMasterRefferedBack, $updatedPurchaseReturnMasterRefferedBack->toArray());
        $dbPurchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepo->find($purchaseReturnMasterRefferedBack->id);
        $this->assertModelData($fakePurchaseReturnMasterRefferedBack, $dbPurchaseReturnMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->create();

        $resp = $this->purchaseReturnMasterRefferedBackRepo->delete($purchaseReturnMasterRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(PurchaseReturnMasterRefferedBack::find($purchaseReturnMasterRefferedBack->id), 'PurchaseReturnMasterRefferedBack should not exist in DB');
    }
}
