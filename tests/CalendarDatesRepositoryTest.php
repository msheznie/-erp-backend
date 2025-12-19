<?php namespace Tests\Repositories;

use App\Models\CalendarDates;
use App\Repositories\CalendarDatesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CalendarDatesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CalendarDatesRepository
     */
    protected $calendarDatesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->calendarDatesRepo = \App::make(CalendarDatesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->make()->toArray();

        $createdCalendarDates = $this->calendarDatesRepo->create($calendarDates);

        $createdCalendarDates = $createdCalendarDates->toArray();
        $this->assertArrayHasKey('id', $createdCalendarDates);
        $this->assertNotNull($createdCalendarDates['id'], 'Created CalendarDates must have id specified');
        $this->assertNotNull(CalendarDates::find($createdCalendarDates['id']), 'CalendarDates with given id must be in DB');
        $this->assertModelData($calendarDates, $createdCalendarDates);
    }

    /**
     * @test read
     */
    public function test_read_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->create();

        $dbCalendarDates = $this->calendarDatesRepo->find($calendarDates->id);

        $dbCalendarDates = $dbCalendarDates->toArray();
        $this->assertModelData($calendarDates->toArray(), $dbCalendarDates);
    }

    /**
     * @test update
     */
    public function test_update_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->create();
        $fakeCalendarDates = factory(CalendarDates::class)->make()->toArray();

        $updatedCalendarDates = $this->calendarDatesRepo->update($fakeCalendarDates, $calendarDates->id);

        $this->assertModelData($fakeCalendarDates, $updatedCalendarDates->toArray());
        $dbCalendarDates = $this->calendarDatesRepo->find($calendarDates->id);
        $this->assertModelData($fakeCalendarDates, $dbCalendarDates->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_calendar_dates()
    {
        $calendarDates = factory(CalendarDates::class)->create();

        $resp = $this->calendarDatesRepo->delete($calendarDates->id);

        $this->assertTrue($resp);
        $this->assertNull(CalendarDates::find($calendarDates->id), 'CalendarDates should not exist in DB');
    }
}
