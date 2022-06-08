<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CalendarDates;

class CalendarDatesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/calendar_dates', $calendarDates
        );

        $this->assertApiResponse($calendarDates);
    }

    /**
     * @test
     */
    public function test_read_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/calendar_dates/'.$calendarDates->id
        );

        $this->assertApiResponse($calendarDates->toArray());
    }

    /**
     * @test
     */
    public function test_update_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->create();
        $editedCalendarDates = factory(CalendarDates::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/calendar_dates/'.$calendarDates->id,
            $editedCalendarDates
        );

        $this->assertApiResponse($editedCalendarDates);
    }

    /**
     * @test
     */
    public function test_delete_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/calendar_dates/'.$calendarDates->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/calendar_dates/'.$calendarDates->id
        );

        $this->response->assertStatus(404);
    }
}
