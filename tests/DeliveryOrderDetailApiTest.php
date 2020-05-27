<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DeliveryOrderDetail;

class DeliveryOrderDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/delivery_order_details', $deliveryOrderDetail
        );

        $this->assertApiResponse($deliveryOrderDetail);
    }

    /**
     * @test
     */
    public function test_read_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/delivery_order_details/'.$deliveryOrderDetail->id
        );

        $this->assertApiResponse($deliveryOrderDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->create();
        $editedDeliveryOrderDetail = factory(DeliveryOrderDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/delivery_order_details/'.$deliveryOrderDetail->id,
            $editedDeliveryOrderDetail
        );

        $this->assertApiResponse($editedDeliveryOrderDetail);
    }

    /**
     * @test
     */
    public function test_delete_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/delivery_order_details/'.$deliveryOrderDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/delivery_order_details/'.$deliveryOrderDetail->id
        );

        $this->response->assertStatus(404);
    }
}
