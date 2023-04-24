<?php namespace Tests\Repositories;

use App\Models\CalendarDatesDetailEditLog;
use App\Repositories\CalendarDatesDetailEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CalendarDatesDetailEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CalendarDatesDetailEditLogRepository
     */
    protected $calendarDatesDetailEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->calendarDatesDetailEditLogRepo = \App::make(CalendarDatesDetailEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->make()->toArray();

        $createdCalendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepo->create($calendarDatesDetailEditLog);

        $createdCalendarDatesDetailEditLog = $createdCalendarDatesDetailEditLog->toArray();
        $this->assertArrayHasKey('id', $createdCalendarDatesDetailEditLog);
        $this->assertNotNull($createdCalendarDatesDetailEditLog['id'], 'Created CalendarDatesDetailEditLog must have id specified');
        $this->assertNotNull(CalendarDatesDetailEditLog::find($createdCalendarDatesDetailEditLog['id']), 'CalendarDatesDetailEditLog with given id must be in DB');
        $this->assertModelData($calendarDatesDetailEditLog, $createdCalendarDatesDetailEditLog);
    }

    /**
     * @test read
     */
    public function test_read_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->create();

        $dbCalendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepo->find($calendarDatesDetailEditLog->id);

        $dbCalendarDatesDetailEditLog = $dbCalendarDatesDetailEditLog->toArray();
        $this->assertModelData($calendarDatesDetailEditLog->toArray(), $dbCalendarDatesDetailEditLog);
    }

    /**
     * @test update
     */
    public function test_update_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->create();
        $fakeCalendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->make()->toArray();

        $updatedCalendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepo->update($fakeCalendarDatesDetailEditLog, $calendarDatesDetailEditLog->id);

        $this->assertModelData($fakeCalendarDatesDetailEditLog, $updatedCalendarDatesDetailEditLog->toArray());
        $dbCalendarDatesDetailEditLog = $this->calendarDatesDetailEditLogRepo->find($calendarDatesDetailEditLog->id);
        $this->assertModelData($fakeCalendarDatesDetailEditLog, $dbCalendarDatesDetailEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_calendar_dates_detail_edit_log()
    {
        $calendarDatesDetailEditLog = factory(CalendarDatesDetailEditLog::class)->create();

        $resp = $this->calendarDatesDetailEditLogRepo->delete($calendarDatesDetailEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(CalendarDatesDetailEditLog::find($calendarDatesDetailEditLog->id), 'CalendarDatesDetailEditLog should not exist in DB');
    }
}
