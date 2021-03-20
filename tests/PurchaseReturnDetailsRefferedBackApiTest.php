<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PurchaseReturnDetailsRefferedBack;

class PurchaseReturnDetailsRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/purchase_return_details_reffered_backs', $purchaseReturnDetailsRefferedBack
        );

        $this->assertApiResponse($purchaseReturnDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/purchase_return_details_reffered_backs/'.$purchaseReturnDetailsRefferedBack->id
        );

        $this->assertApiResponse($purchaseReturnDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->create();
        $editedPurchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/purchase_return_details_reffered_backs/'.$purchaseReturnDetailsRefferedBack->id,
            $editedPurchaseReturnDetailsRefferedBack
        );

        $this->assertApiResponse($editedPurchaseReturnDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_purchase_return_details_reffered_back()
    {
        $purchaseReturnDetailsRefferedBack = factory(PurchaseReturnDetailsRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/purchase_return_details_reffered_backs/'.$purchaseReturnDetailsRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/purchase_return_details_reffered_backs/'.$purchaseReturnDetailsRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}
