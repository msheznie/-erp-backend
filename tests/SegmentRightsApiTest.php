<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSegmentRightsTrait;
use Tests\ApiTestTrait;

class SegmentRightsApiTest extends TestCase
{
    use MakeSegmentRightsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_segment_rights()
    {
        $segmentRights = $this->fakeSegmentRightsData();
        $this->response = $this->json('POST', '/api/segmentRights', $segmentRights);

        $this->assertApiResponse($segmentRights);
    }

    /**
     * @test
     */
    public function test_read_segment_rights()
    {
        $segmentRights = $this->makeSegmentRights();
        $this->response = $this->json('GET', '/api/segmentRights/'.$segmentRights->id);

        $this->assertApiResponse($segmentRights->toArray());
    }

    /**
     * @test
     */
    public function test_update_segment_rights()
    {
        $segmentRights = $this->makeSegmentRights();
        $editedSegmentRights = $this->fakeSegmentRightsData();

        $this->response = $this->json('PUT', '/api/segmentRights/'.$segmentRights->id, $editedSegmentRights);

        $this->assertApiResponse($editedSegmentRights);
    }

    /**
     * @test
     */
    public function test_delete_segment_rights()
    {
        $segmentRights = $this->makeSegmentRights();
        $this->response = $this->json('DELETE', '/api/segmentRights/'.$segmentRights->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/segmentRights/'.$segmentRights->id);

        $this->response->assertStatus(404);
    }
}
