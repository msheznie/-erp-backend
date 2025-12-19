<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PurchaseReturnLogistic;

class PurchaseReturnLogisticApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/purchase_return_logistics', $purchaseReturnLogistic
        );

        $this->assertApiResponse($purchaseReturnLogistic);
    }

    /**
     * @test
     */
    public function test_read_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/purchase_return_logistics/'.$purchaseReturnLogistic->id
        );

        $this->assertApiResponse($purchaseReturnLogistic->toArray());
    }

    /**
     * @test
     */
    public function test_update_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->create();
        $editedPurchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/purchase_return_logistics/'.$purchaseReturnLogistic->id,
            $editedPurchaseReturnLogistic
        );

        $this->assertApiResponse($editedPurchaseReturnLogistic);
    }

    /**
     * @test
     */
    public function test_delete_purchase_return_logistic()
    {
        $purchaseReturnLogistic = factory(PurchaseReturnLogistic::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/purchase_return_logistics/'.$purchaseReturnLogistic->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/purchase_return_logistics/'.$purchaseReturnLogistic->id
        );

        $this->response->assertStatus(404);
    }
}
