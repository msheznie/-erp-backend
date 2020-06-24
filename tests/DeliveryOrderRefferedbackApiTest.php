<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DeliveryOrderRefferedback;

class DeliveryOrderRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/delivery_order_refferedbacks', $deliveryOrderRefferedback
        );

        $this->assertApiResponse($deliveryOrderRefferedback);
    }

    /**
     * @test
     */
    public function test_read_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/delivery_order_refferedbacks/'.$deliveryOrderRefferedback->id
        );

        $this->assertApiResponse($deliveryOrderRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->create();
        $editedDeliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/delivery_order_refferedbacks/'.$deliveryOrderRefferedback->id,
            $editedDeliveryOrderRefferedback
        );

        $this->assertApiResponse($editedDeliveryOrderRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/delivery_order_refferedbacks/'.$deliveryOrderRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/delivery_order_refferedbacks/'.$deliveryOrderRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}
