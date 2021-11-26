<?php namespace Tests\Repositories;

use App\Models\WeekDays;
use App\Repositories\WeekDaysRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class WeekDaysRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var WeekDaysRepository
     */
    protected $weekDaysRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->weekDaysRepo = \App::make(WeekDaysRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_week_days()
    {
        $weekDays = factory(WeekDays::class)->make()->toArray();

        $createdWeekDays = $this->weekDaysRepo->create($weekDays);

        $createdWeekDays = $createdWeekDays->toArray();
        $this->assertArrayHasKey('id', $createdWeekDays);
        $this->assertNotNull($createdWeekDays['id'], 'Created WeekDays must have id specified');
        $this->assertNotNull(WeekDays::find($createdWeekDays['id']), 'WeekDays with given id must be in DB');
        $this->assertModelData($weekDays, $createdWeekDays);
    }

    /**
     * @test read
     */
    public function test_read_week_days()
    {
        $weekDays = factory(WeekDays::class)->create();

        $dbWeekDays = $this->weekDaysRepo->find($weekDays->id);

        $dbWeekDays = $dbWeekDays->toArray();
        $this->assertModelData($weekDays->toArray(), $dbWeekDays);
    }

    /**
     * @test update
     */
    public function test_update_week_days()
    {
        $weekDays = factory(WeekDays::class)->create();
        $fakeWeekDays = factory(WeekDays::class)->make()->toArray();

        $updatedWeekDays = $this->weekDaysRepo->update($fakeWeekDays, $weekDays->id);

        $this->assertModelData($fakeWeekDays, $updatedWeekDays->toArray());
        $dbWeekDays = $this->weekDaysRepo->find($weekDays->id);
        $this->assertModelData($fakeWeekDays, $dbWeekDays->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_week_days()
    {
        $weekDays = factory(WeekDays::class)->create();

        $resp = $this->weekDaysRepo->delete($weekDays->id);

        $this->assertTrue($resp);
        $this->assertNull(WeekDays::find($weekDays->id), 'WeekDays should not exist in DB');
    }
}
