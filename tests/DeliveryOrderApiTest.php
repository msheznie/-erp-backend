<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DeliveryOrder;

class DeliveryOrderApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/delivery_orders', $deliveryOrder
        );

        $this->assertApiResponse($deliveryOrder);
    }

    /**
     * @test
     */
    public function test_read_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/delivery_orders/'.$deliveryOrder->id
        );

        $this->assertApiResponse($deliveryOrder->toArray());
    }

    /**
     * @test
     */
    public function test_update_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->create();
        $editedDeliveryOrder = factory(DeliveryOrder::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/delivery_orders/'.$deliveryOrder->id,
            $editedDeliveryOrder
        );

        $this->assertApiResponse($editedDeliveryOrder);
    }

    /**
     * @test
     */
    public function test_delete_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/delivery_orders/'.$deliveryOrder->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/delivery_orders/'.$deliveryOrder->id
        );

        $this->response->assertStatus(404);
    }
}
