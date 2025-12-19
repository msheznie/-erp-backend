<?php namespace Tests\Repositories;

use App\Models\PurchaseReturnDetailsRefferedBack;
use App\Repositories\PurchaseReturnDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PurchaseReturnDetailsRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseReturnDetailsRefferedBackRepository
     */
    protected $purchaseReturnDetailsRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->purchaseReturnDetailsRefferedBackRepo = \App::make(PurchaseReturnDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->make()->toArray();

        $createdPurchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepo->create($purchaseReturnDetailsRefferedBack);

        $createdPurchaseReturnDetailsRefferedBack = $createdPurchaseReturnDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseReturnDetailsRefferedBack);
        $this->assertNotNull($createdPurchaseReturnDetailsRefferedBack['id'], 'Created PurchaseReturnDetailsRefferedBack must have id specified');
        $this->assertNotNull(PurchaseReturnDetailsRefferedBack::find($createdPurchaseReturnDetailsRefferedBack['id']), 'PurchaseReturnDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($purchaseReturnDetailsRefferedBack, $createdPurchaseReturnDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->create();

        $dbPurchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepo->find($purchaseReturnDetailsRefferedBack->id);

        $dbPurchaseReturnDetailsRefferedBack = $dbPurchaseReturnDetailsRefferedBack->toArray();
        $this->assertModelData($purchaseReturnDetailsRefferedBack->toArray(), $dbPurchaseReturnDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->create();
        $fakePurchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->make()->toArray();

        $updatedPurchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepo->update($fakePurchaseReturnDetailsRefferedBack, $purchaseReturnDetailsRefferedBack->id);

        $this->assertModelData($fakePurchaseReturnDetailsRefferedBack, $updatedPurchaseReturnDetailsRefferedBack->toArray());
        $dbPurchaseReturnDetailsRefferedBack = $this->purchaseReturnDetailsRefferedBackRepo->find($purchaseReturnDetailsRefferedBack->id);
        $this->assertModelData($fakePurchaseReturnDetailsRefferedBack, $dbPurchaseReturnDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->create();

        $resp = $this->purchaseReturnDetailsRefferedBackRepo->delete($purchaseReturnDetailsRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(PurchaseReturnDetailsRefferedBack::find($purchaseReturnDetailsRefferedBack->id), 'PurchaseReturnDetailsRefferedBack should not exist in DB');
    }
}
