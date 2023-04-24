<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CalendarDatesDetailEditLog;

class CalendarDatesDetailEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/calendar_dates_detail_edit_logs', $calendarDatesDetailEditLog
        );

        $this->assertApiResponse($calendarDatesDetailEditLog);
    }

    /**
     * @test
     */
    public function test_read_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/calendar_dates_detail_edit_logs/'.$calendarDatesDetailEditLog->id
        );

        $this->assertApiResponse($calendarDatesDetailEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->create();
        $editedCalendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/calendar_dates_detail_edit_logs/'.$calendarDatesDetailEditLog->id,
            $editedCalendarDatesDetailEditLog
        );

        $this->assertApiResponse($editedCalendarDatesDetailEditLog);
    }

    /**
     * @test
     */
    public function test_delete_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/calendar_dates_detail_edit_logs/'.$calendarDatesDetailEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/calendar_dates_detail_edit_logs/'.$calendarDatesDetailEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
