<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderCategoryApiTest extends TestCase
{
    use MakePurchaseOrderCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->fakePurchaseOrderCategoryData();
        $this->json('POST', '/api/v1/purchaseOrderCategories', $purchaseOrderCategory);

        $this->assertApiResponse($purchaseOrderCategory);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->makePurchaseOrderCategory();
        $this->json('GET', '/api/v1/purchaseOrderCategories/'.$purchaseOrderCategory->id);

        $this->assertApiResponse($purchaseOrderCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->makePurchaseOrderCategory();
        $editedPurchaseOrderCategory = $this->fakePurchaseOrderCategoryData();

        $this->json('PUT', '/api/v1/purchaseOrderCategories/'.$purchaseOrderCategory->id, $editedPurchaseOrderCategory);

        $this->assertApiResponse($editedPurchaseOrderCategory);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderCategory()
    {
        $purchaseOrderCategory = $this->makePurchaseOrderCategory();
        $this->json('DELETE', '/api/v1/purchaseOrderCategories/'.$purchaseOrderCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderCategories/'.$purchaseOrderCategory->id);

        $this->assertResponseStatus(404);
    }
}
