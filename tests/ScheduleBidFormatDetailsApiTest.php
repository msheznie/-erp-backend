<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ScheduleBidFormatDetails;

class ScheduleBidFormatDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/schedule_bid_format_details', $scheduleBidFormatDetails
        );

        $this->assertApiResponse($scheduleBidFormatDetails);
    }

    /**
     * @test
     */
    public function test_read_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/schedule_bid_format_details/'.$scheduleBidFormatDetails->id
        );

        $this->assertApiResponse($scheduleBidFormatDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->create();
        $editedScheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/schedule_bid_format_details/'.$scheduleBidFormatDetails->id,
            $editedScheduleBidFormatDetails
        );

        $this->assertApiResponse($editedScheduleBidFormatDetails);
    }

    /**
     * @test
     */
    public function test_delete_schedule_bid_format_details()
    {
        $scheduleBidFormatDetails = factory(ScheduleBidFormatDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/schedule_bid_format_details/'.$scheduleBidFormatDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/schedule_bid_format_details/'.$scheduleBidFormatDetails->id
        );

        $this->response->assertStatus(404);
    }
}
