<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ItemSerial;

class ItemSerialApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/item_serials', $itemSerial
        );

        $this->assertApiResponse($itemSerial);
    }

    /**
     * @test
     */
    public function test_read_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/item_serials/'.$itemSerial->id
        );

        $this->assertApiResponse($itemSerial->toArray());
    }

    /**
     * @test
     */
    public function test_update_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->create();
        $editedItemSerial = factory(ItemSerial::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/item_serials/'.$itemSerial->id,
            $editedItemSerial
        );

        $this->assertApiResponse($editedItemSerial);
    }

    /**
     * @test
     */
    public function test_delete_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/item_serials/'.$itemSerial->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/item_serials/'.$itemSerial->id
        );

        $this->response->assertStatus(404);
    }
}
