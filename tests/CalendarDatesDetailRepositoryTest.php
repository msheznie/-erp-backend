<?php namespace Tests\Repositories;

use App\Models\CalendarDatesDetail;
use App\Repositories\CalendarDatesDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CalendarDatesDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CalendarDatesDetailRepository
     */
    protected $calendarDatesDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->calendarDatesDetailRepo = \App::make(CalendarDatesDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_calendar_dates_detail()
    {
        $calendarDatesDetail = factory(CalendarDatesDetail::class)->make()->toArray();

        $createdCalendarDatesDetail = $this->calendarDatesDetailRepo->create($calendarDatesDetail);

        $createdCalendarDatesDetail = $createdCalendarDatesDetail->toArray();
        $this->assertArrayHasKey('id', $createdCalendarDatesDetail);
        $this->assertNotNull($createdCalendarDatesDetail['id'], 'Created CalendarDatesDetail must have id specified');
        $this->assertNotNull(CalendarDatesDetail::find($createdCalendarDatesDetail['id']), 'CalendarDatesDetail with given id must be in DB');
        $this->assertModelData($calendarDatesDetail, $createdCalendarDatesDetail);
    }

    /**
     * @test read
     */
    public function test_read_calendar_dates_detail()
    {
        $calendarDatesDetail = factory(CalendarDatesDetail::class)->create();

        $dbCalendarDatesDetail = $this->calendarDatesDetailRepo->find($calendarDatesDetail->id);

        $dbCalendarDatesDetail = $dbCalendarDatesDetail->toArray();
        $this->assertModelData($calendarDatesDetail->toArray(), $dbCalendarDatesDetail);
    }

    /**
     * @test update
     */
    public function test_update_calendar_dates_detail()
    {
        $calendarDatesDetail = factory(CalendarDatesDetail::class)->create();
        $fakeCalendarDatesDetail = factory(CalendarDatesDetail::class)->make()->toArray();

        $updatedCalendarDatesDetail = $this->calendarDatesDetailRepo->update($fakeCalendarDatesDetail, $calendarDatesDetail->id);

        $this->assertModelData($fakeCalendarDatesDetail, $updatedCalendarDatesDetail->toArray());
        $dbCalendarDatesDetail = $this->calendarDatesDetailRepo->find($calendarDatesDetail->id);
        $this->assertModelData($fakeCalendarDatesDetail, $dbCalendarDatesDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_calendar_dates_detail()
    {
        $calendarDatesDetail = factory(CalendarDatesDetail::class)->create();

        $resp = $this->calendarDatesDetailRepo->delete($calendarDatesDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(CalendarDatesDetail::find($calendarDatesDetail->id), 'CalendarDatesDetail should not exist in DB');
    }
}
