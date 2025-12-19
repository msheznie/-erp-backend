<?php

use App\Models\PurchaseOrderCategory;
use App\Repositories\PurchaseOrderCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderCategoryRepositoryTest extends TestCase
{
    use MakePurchaseOrderCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PurchaseOrderCategoryRepository
     */
    protected $purchaseOrderCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->purchaseOrderCategoryRepo = App::make(PurchaseOrderCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->fakePurchaseOrderCategoryData();
        $createdPurchaseOrderCategory = $this->purchaseOrderCategoryRepo->create($purchaseOrderCategory);
        $createdPurchaseOrderCategory = $createdPurchaseOrderCategory->toArray();
        $this->assertArrayHasKey('id', $createdPurchaseOrderCategory);
        $this->assertNotNull($createdPurchaseOrderCategory['id'], 'Created PurchaseOrderCategory must have id specified');
        $this->assertNotNull(PurchaseOrderCategory::find($createdPurchaseOrderCategory['id']), 'PurchaseOrderCategory with given id must be in DB');
        $this->assertModelData($purchaseOrderCategory, $createdPurchaseOrderCategory);
    }

    /**
     * @test read
     */
    public function testReadPurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->makePurchaseOrderCategory();
        $dbPurchaseOrderCategory = $this->purchaseOrderCategoryRepo->find($purchaseOrderCategory->id);
        $dbPurchaseOrderCategory = $dbPurchaseOrderCategory->toArray();
        $this->assertModelData($purchaseOrderCategory->toArray(), $dbPurchaseOrderCategory);
    }

    /**
     * @test update
     */
    public function testUpdatePurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->makePurchaseOrderCategory();
        $fakePurchaseOrderCategory = $this->fakePurchaseOrderCategoryData();
        $updatedPurchaseOrderCategory = $this->purchaseOrderCategoryRepo->update($fakePurchaseOrderCategory, $purchaseOrderCategory->id);
        $this->assertModelData($fakePurchaseOrderCategory, $updatedPurchaseOrderCategory->toArray());
        $dbPurchaseOrderCategory = $this->purchaseOrderCategoryRepo->find($purchaseOrderCategory->id);
        $this->assertModelData($fakePurchaseOrderCategory, $dbPurchaseOrderCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->makePurchaseOrderCategory();
        $resp = $this->purchaseOrderCategoryRepo->delete($purchaseOrderCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(PurchaseOrderCategory::find($purchaseOrderCategory->id), 'PurchaseOrderCategory should not exist in DB');
    }
}
