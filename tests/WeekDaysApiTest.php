<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\WeekDays;

class WeekDaysApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_week_days()
    {
        $weekDays = factory(WeekDays::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/week_days', $weekDays
        );

        $this->assertApiResponse($weekDays);
    }

    /**
     * @test
     */
    public function test_read_week_days()
    {
        $weekDays = factory(WeekDays::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/week_days/'.$weekDays->id
        );

        $this->assertApiResponse($weekDays->toArray());
    }

    /**
     * @test
     */
    public function test_update_week_days()
    {
        $weekDays = factory(WeekDays::class)->create();
        $editedWeekDays = factory(WeekDays::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/week_days/'.$weekDays->id,
            $editedWeekDays
        );

        $this->assertApiResponse($editedWeekDays);
    }

    /**
     * @test
     */
    public function test_delete_week_days()
    {
        $weekDays = factory(WeekDays::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/week_days/'.$weekDays->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/week_days/'.$weekDays->id
        );

        $this->response->assertStatus(404);
    }
}
