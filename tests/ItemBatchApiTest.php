<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ItemBatch;

class ItemBatchApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/item_batches', $itemBatch
        );

        $this->assertApiResponse($itemBatch);
    }

    /**
     * @test
     */
    public function test_read_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/item_batches/'.$itemBatch->id
        );

        $this->assertApiResponse($itemBatch->toArray());
    }

    /**
     * @test
     */
    public function test_update_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->create();
        $editedItemBatch = factory(ItemBatch::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/item_batches/'.$itemBatch->id,
            $editedItemBatch
        );

        $this->assertApiResponse($editedItemBatch);
    }

    /**
     * @test
     */
    public function test_delete_item_batch()
    {
        $itemBatch = factory(ItemBatch::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/item_batches/'.$itemBatch->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/item_batches/'.$itemBatch->id
        );

        $this->response->assertStatus(404);
    }
}
