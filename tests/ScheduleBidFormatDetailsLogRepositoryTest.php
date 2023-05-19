<?php namespace Tests\Repositories;

use App\Models\ScheduleBidFormatDetailsLog;
use App\Repositories\ScheduleBidFormatDetailsLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ScheduleBidFormatDetailsLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ScheduleBidFormatDetailsLogRepository
     */
    protected $scheduleBidFormatDetailsLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->scheduleBidFormatDetailsLogRepo = \App::make(ScheduleBidFormatDetailsLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->make()->toArray();

        $createdScheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepo->create($scheduleBidFormatDetailsLog);

        $createdScheduleBidFormatDetailsLog = $createdScheduleBidFormatDetailsLog->toArray();
        $this->assertArrayHasKey('id', $createdScheduleBidFormatDetailsLog);
        $this->assertNotNull($createdScheduleBidFormatDetailsLog['id'], 'Created ScheduleBidFormatDetailsLog must have id specified');
        $this->assertNotNull(ScheduleBidFormatDetailsLog::find($createdScheduleBidFormatDetailsLog['id']), 'ScheduleBidFormatDetailsLog with given id must be in DB');
        $this->assertModelData($scheduleBidFormatDetailsLog, $createdScheduleBidFormatDetailsLog);
    }

    /**
     * @test read
     */
    public function test_read_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->create();

        $dbScheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepo->find($scheduleBidFormatDetailsLog->id);

        $dbScheduleBidFormatDetailsLog = $dbScheduleBidFormatDetailsLog->toArray();
        $this->assertModelData($scheduleBidFormatDetailsLog->toArray(), $dbScheduleBidFormatDetailsLog);
    }

    /**
     * @test update
     */
    public function test_update_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->create();
        $fakeScheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->make()->toArray();

        $updatedScheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepo->update($fakeScheduleBidFormatDetailsLog, $scheduleBidFormatDetailsLog->id);

        $this->assertModelData($fakeScheduleBidFormatDetailsLog, $updatedScheduleBidFormatDetailsLog->toArray());
        $dbScheduleBidFormatDetailsLog = $this->scheduleBidFormatDetailsLogRepo->find($scheduleBidFormatDetailsLog->id);
        $this->assertModelData($fakeScheduleBidFormatDetailsLog, $dbScheduleBidFormatDetailsLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_schedule_bid_format_details_log()
    {
        $scheduleBidFormatDetailsLog = factory(ScheduleBidFormatDetailsLog::class)->create();

        $resp = $this->scheduleBidFormatDetailsLogRepo->delete($scheduleBidFormatDetailsLog->id);

        $this->assertTrue($resp);
        $this->assertNull(ScheduleBidFormatDetailsLog::find($scheduleBidFormatDetailsLog->id), 'ScheduleBidFormatDetailsLog should not exist in DB');
    }
}
