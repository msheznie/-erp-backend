<?php namespace Tests\Repositories;

use App\Models\PurchaseReturnLogistic;
use App\Repositories\PurchaseReturnLogisticRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PurchaseReturnLogisticRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseReturnLogisticRepository
     */
    protected $purchaseReturnLogisticRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->purchaseReturnLogisticRepo = \App::make(PurchaseReturnLogisticRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->make()->toArray();

        $createdPurchaseReturnLogistic = $this->purchaseReturnLogisticRepo->create($purchaseReturnLogistic);

        $createdPurchaseReturnLogistic = $createdPurchaseReturnLogistic->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseReturnLogistic);
        $this->assertNotNull($createdPurchaseReturnLogistic['id'], 'Created PurchaseReturnLogistic must have id specified');
        $this->assertNotNull(PurchaseReturnLogistic::find($createdPurchaseReturnLogistic['id']), 'PurchaseReturnLogistic with given id must be in DB');
        $this->assertModelData($purchaseReturnLogistic, $createdPurchaseReturnLogistic);
    }

    /**
     * @test read
     */
    public function test_read_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->create();

        $dbPurchaseReturnLogistic = $this->purchaseReturnLogisticRepo->find($purchaseReturnLogistic->id);

        $dbPurchaseReturnLogistic = $dbPurchaseReturnLogistic->toArray();
        $this->assertModelData($purchaseReturnLogistic->toArray(), $dbPurchaseReturnLogistic);
    }

    /**
     * @test update
     */
    public function test_update_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->create();
        $fakePurchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->make()->toArray();

        $updatedPurchaseReturnLogistic = $this->purchaseReturnLogisticRepo->update($fakePurchaseReturnLogistic, $purchaseReturnLogistic->id);

        $this->assertModelData($fakePurchaseReturnLogistic, $updatedPurchaseReturnLogistic->toArray());
        $dbPurchaseReturnLogistic = $this->purchaseReturnLogisticRepo->find($purchaseReturnLogistic->id);
        $this->assertModelData($fakePurchaseReturnLogistic, $dbPurchaseReturnLogistic->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->create();

        $resp = $this->purchaseReturnLogisticRepo->delete($purchaseReturnLogistic->id);

        $this->assertTrue($resp);
        $this->assertNull(PurchaseReturnLogistic::find($purchaseReturnLogistic->id), 'PurchaseReturnLogistic should not exist in DB');
    }
}
