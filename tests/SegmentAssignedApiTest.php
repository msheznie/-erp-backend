<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SegmentAssigned;

class SegmentAssignedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/segment_assigneds', $segmentAssigned
        );

        $this->assertApiResponse($segmentAssigned);
    }

    /**
     * @test
     */
    public function test_read_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/segment_assigneds/'.$segmentAssigned->id
        );

        $this->assertApiResponse($segmentAssigned->toArray());
    }

    /**
     * @test
     */
    public function test_update_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->create();
        $editedSegmentAssigned = factory(SegmentAssigned::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/segment_assigneds/'.$segmentAssigned->id,
            $editedSegmentAssigned
        );

        $this->assertApiResponse($editedSegmentAssigned);
    }

    /**
     * @test
     */
    public function test_delete_segment_assigned()
    {
        $segmentAssigned = factory(SegmentAssigned::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/segment_assigneds/'.$segmentAssigned->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/segment_assigneds/'.$segmentAssigned->id
        );

        $this->response->assertStatus(404);
    }
}
