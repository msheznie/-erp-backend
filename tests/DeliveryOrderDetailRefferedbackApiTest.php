<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DeliveryOrderDetailRefferedback;

class DeliveryOrderDetailRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/delivery_order_detail_refferedbacks', $deliveryOrderDetailRefferedback
        );

        $this->assertApiResponse($deliveryOrderDetailRefferedback);
    }

    /**
     * @test
     */
    public function test_read_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/delivery_order_detail_refferedbacks/'.$deliveryOrderDetailRefferedback->id
        );

        $this->assertApiResponse($deliveryOrderDetailRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->create();
        $editedDeliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/delivery_order_detail_refferedbacks/'.$deliveryOrderDetailRefferedback->id,
            $editedDeliveryOrderDetailRefferedback
        );

        $this->assertApiResponse($editedDeliveryOrderDetailRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/delivery_order_detail_refferedbacks/'.$deliveryOrderDetailRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/delivery_order_detail_refferedbacks/'.$deliveryOrderDetailRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}
