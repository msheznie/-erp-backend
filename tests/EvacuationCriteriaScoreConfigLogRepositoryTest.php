<?php namespace Tests\Repositories;

use App\Models\EvacuationCriteriaScoreConfigLog;
use App\Repositories\EvacuationCriteriaScoreConfigLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvacuationCriteriaScoreConfigLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvacuationCriteriaScoreConfigLogRepository
     */
    protected $evacuationCriteriaScoreConfigLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evacuationCriteriaScoreConfigLogRepo = \App::make(EvacuationCriteriaScoreConfigLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->make()->toArray();

        $createdEvacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepo->create($evacuationCriteriaScoreConfigLog);

        $createdEvacuationCriteriaScoreConfigLog = $createdEvacuationCriteriaScoreConfigLog->toArray();
        $this->assertArrayHasKey('id', $createdEvacuationCriteriaScoreConfigLog);
        $this->assertNotNull($createdEvacuationCriteriaScoreConfigLog['id'], 'Created EvacuationCriteriaScoreConfigLog must have id specified');
        $this->assertNotNull(EvacuationCriteriaScoreConfigLog::find($createdEvacuationCriteriaScoreConfigLog['id']), 'EvacuationCriteriaScoreConfigLog with given id must be in DB');
        $this->assertModelData($evacuationCriteriaScoreConfigLog, $createdEvacuationCriteriaScoreConfigLog);
    }

    /**
     * @test read
     */
    public function test_read_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->create();

        $dbEvacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepo->find($evacuationCriteriaScoreConfigLog->id);

        $dbEvacuationCriteriaScoreConfigLog = $dbEvacuationCriteriaScoreConfigLog->toArray();
        $this->assertModelData($evacuationCriteriaScoreConfigLog->toArray(), $dbEvacuationCriteriaScoreConfigLog);
    }

    /**
     * @test update
     */
    public function test_update_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->create();
        $fakeEvacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->make()->toArray();

        $updatedEvacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepo->update($fakeEvacuationCriteriaScoreConfigLog, $evacuationCriteriaScoreConfigLog->id);

        $this->assertModelData($fakeEvacuationCriteriaScoreConfigLog, $updatedEvacuationCriteriaScoreConfigLog->toArray());
        $dbEvacuationCriteriaScoreConfigLog = $this->evacuationCriteriaScoreConfigLogRepo->find($evacuationCriteriaScoreConfigLog->id);
        $this->assertModelData($fakeEvacuationCriteriaScoreConfigLog, $dbEvacuationCriteriaScoreConfigLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evacuation_criteria_score_config_log()
    {
        $evacuationCriteriaScoreConfigLog = factory(EvacuationCriteriaScoreConfigLog::class)->create();

        $resp = $this->evacuationCriteriaScoreConfigLogRepo->delete($evacuationCriteriaScoreConfigLog->id);

        $this->assertTrue($resp);
        $this->assertNull(EvacuationCriteriaScoreConfigLog::find($evacuationCriteriaScoreConfigLog->id), 'EvacuationCriteriaScoreConfigLog should not exist in DB');
    }
}
