<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ScheduleBidFormatDetailsLog;

class ScheduleBidFormatDetailsLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/schedule_bid_format_details_logs', $scheduleBidFormatDetailsLog
        );

        $this->assertApiResponse($scheduleBidFormatDetailsLog);
    }

    /**
     * @test
     */
    public function test_read_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/schedule_bid_format_details_logs/'.$scheduleBidFormatDetailsLog->id
        );

        $this->assertApiResponse($scheduleBidFormatDetailsLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->create();
        $editedScheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/schedule_bid_format_details_logs/'.$scheduleBidFormatDetailsLog->id,
            $editedScheduleBidFormatDetailsLog
        );

        $this->assertApiResponse($editedScheduleBidFormatDetailsLog);
    }

    /**
     * @test
     */
    public function test_delete_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/schedule_bid_format_details_logs/'.$scheduleBidFormatDetailsLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/schedule_bid_format_details_logs/'.$scheduleBidFormatDetailsLog->id
        );

        $this->response->assertStatus(404);
    }
}
