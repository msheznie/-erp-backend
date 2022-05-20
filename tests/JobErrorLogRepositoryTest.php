<?php namespace Tests\Repositories;

use App\Models\JobErrorLog;
use App\Repositories\JobErrorLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class JobErrorLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var JobErrorLogRepository
     */
    protected $jobErrorLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->jobErrorLogRepo = \App::make(JobErrorLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->make()->toArray();

        $createdJobErrorLog = $this->jobErrorLogRepo->create($jobErrorLog);

        $createdJobErrorLog = $createdJobErrorLog->toArray();
        $this->assertArrayHasKey('id', $createdJobErrorLog);
        $this->assertNotNull($createdJobErrorLog['id'], 'Created JobErrorLog must have id specified');
        $this->assertNotNull(JobErrorLog::find($createdJobErrorLog['id']), 'JobErrorLog with given id must be in DB');
        $this->assertModelData($jobErrorLog, $createdJobErrorLog);
    }

    /**
     * @test read
     */
    public function test_read_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->create();

        $dbJobErrorLog = $this->jobErrorLogRepo->find($jobErrorLog->id);

        $dbJobErrorLog = $dbJobErrorLog->toArray();
        $this->assertModelData($jobErrorLog->toArray(), $dbJobErrorLog);
    }

    /**
     * @test update
     */
    public function test_update_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->create();
        $fakeJobErrorLog = factory(JobErrorLog::class)->make()->toArray();

        $updatedJobErrorLog = $this->jobErrorLogRepo->update($fakeJobErrorLog, $jobErrorLog->id);

        $this->assertModelData($fakeJobErrorLog, $updatedJobErrorLog->toArray());
        $dbJobErrorLog = $this->jobErrorLogRepo->find($jobErrorLog->id);
        $this->assertModelData($fakeJobErrorLog, $dbJobErrorLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_job_error_log()
    {
        $jobErrorLog = factory(JobErrorLog::class)->create();

        $resp = $this->jobErrorLogRepo->delete($jobErrorLog->id);

        $this->assertTrue($resp);
        $this->assertNull(JobErrorLog::find($jobErrorLog->id), 'JobErrorLog should not exist in DB');
    }
}
