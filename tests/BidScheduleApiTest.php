<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidSchedule;

class BidScheduleApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_schedules', $bidSchedule
        );

        $this->assertApiResponse($bidSchedule);
    }

    /**
     * @test
     */
    public function test_read_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_schedules/'.$bidSchedule->id
        );

        $this->assertApiResponse($bidSchedule->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->create();
        $editedBidSchedule = factory(BidSchedule::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_schedules/'.$bidSchedule->id,
            $editedBidSchedule
        );

        $this->assertApiResponse($editedBidSchedule);
    }

    /**
     * @test
     */
    public function test_delete_bid_schedule()
    {
        $bidSchedule = factory(BidSchedule::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_schedules/'.$bidSchedule->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_schedules/'.$bidSchedule->id
        );

        $this->response->assertStatus(404);
    }
}
