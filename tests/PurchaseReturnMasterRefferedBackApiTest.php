<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PurchaseReturnMasterRefferedBack;

class PurchaseReturnMasterRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/purchase_return_master_reffered_backs', $purchaseReturnMasterRefferedBack
        );

        $this->assertApiResponse($purchaseReturnMasterRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/purchase_return_master_reffered_backs/'.$purchaseReturnMasterRefferedBack->id
        );

        $this->assertApiResponse($purchaseReturnMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->create();
        $editedPurchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/purchase_return_master_reffered_backs/'.$purchaseReturnMasterRefferedBack->id,
            $editedPurchaseReturnMasterRefferedBack
        );

        $this->assertApiResponse($editedPurchaseReturnMasterRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_purchase_return_master_reffered_back()
    {
        $purchaseReturnMasterRefferedBack = factory(PurchaseReturnMasterRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/purchase_return_master_reffered_backs/'.$purchaseReturnMasterRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/purchase_return_master_reffered_backs/'.$purchaseReturnMasterRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}
