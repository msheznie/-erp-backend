<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DeliveryTermsMaster;

class DeliveryTermsMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/delivery_terms_masters', $deliveryTermsMaster
        );

        $this->assertApiResponse($deliveryTermsMaster);
    }

    /**
     * @test
     */
    public function test_read_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/delivery_terms_masters/'.$deliveryTermsMaster->id
        );

        $this->assertApiResponse($deliveryTermsMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->create();
        $editedDeliveryTermsMaster = factory(DeliveryTermsMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/delivery_terms_masters/'.$deliveryTermsMaster->id,
            $editedDeliveryTermsMaster
        );

        $this->assertApiResponse($editedDeliveryTermsMaster);
    }

    /**
     * @test
     */
    public function test_delete_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/delivery_terms_masters/'.$deliveryTermsMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/delivery_terms_masters/'.$deliveryTermsMaster->id
        );

        $this->response->assertStatus(404);
    }
}
