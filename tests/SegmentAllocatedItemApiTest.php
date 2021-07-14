<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SegmentAllocatedItem;

class SegmentAllocatedItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/segment_allocated_items', $segmentAllocatedItem
        );

        $this->assertApiResponse($segmentAllocatedItem);
    }

    /**
     * @test
     */
    public function test_read_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/segment_allocated_items/'.$segmentAllocatedItem->id
        );

        $this->assertApiResponse($segmentAllocatedItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->create();
        $editedSegmentAllocatedItem = factory(SegmentAllocatedItem::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/segment_allocated_items/'.$segmentAllocatedItem->id,
            $editedSegmentAllocatedItem
        );

        $this->assertApiResponse($editedSegmentAllocatedItem);
    }

    /**
     * @test
     */
    public function test_delete_segment_allocated_item()
    {
        $segmentAllocatedItem = factory(SegmentAllocatedItem::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/segment_allocated_items/'.$segmentAllocatedItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/segment_allocated_items/'.$segmentAllocatedItem->id
        );

        $this->response->assertStatus(404);
    }
}
