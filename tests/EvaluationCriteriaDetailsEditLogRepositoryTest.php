<?php namespace Tests\Repositories;

use App\Models\EvaluationCriteriaDetailsEditLog;
use App\Repositories\EvaluationCriteriaDetailsEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationCriteriaDetailsEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationCriteriaDetailsEditLogRepository
     */
    protected $evaluationCriteriaDetailsEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationCriteriaDetailsEditLogRepo = \App::make(EvaluationCriteriaDetailsEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->make()->toArray();

        $createdEvaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepo->create($evaluationCriteriaDetailsEditLog);

        $createdEvaluationCriteriaDetailsEditLog = $createdEvaluationCriteriaDetailsEditLog->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationCriteriaDetailsEditLog);
        $this->assertNotNull($createdEvaluationCriteriaDetailsEditLog['id'], 'Created EvaluationCriteriaDetailsEditLog must have id specified');
        $this->assertNotNull(EvaluationCriteriaDetailsEditLog::find($createdEvaluationCriteriaDetailsEditLog['id']), 'EvaluationCriteriaDetailsEditLog with given id must be in DB');
        $this->assertModelData($evaluationCriteriaDetailsEditLog, $createdEvaluationCriteriaDetailsEditLog);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->create();

        $dbEvaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepo->find($evaluationCriteriaDetailsEditLog->id);

        $dbEvaluationCriteriaDetailsEditLog = $dbEvaluationCriteriaDetailsEditLog->toArray();
        $this->assertModelData($evaluationCriteriaDetailsEditLog->toArray(), $dbEvaluationCriteriaDetailsEditLog);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->create();
        $fakeEvaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->make()->toArray();

        $updatedEvaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepo->update($fakeEvaluationCriteriaDetailsEditLog, $evaluationCriteriaDetailsEditLog->id);

        $this->assertModelData($fakeEvaluationCriteriaDetailsEditLog, $updatedEvaluationCriteriaDetailsEditLog->toArray());
        $dbEvaluationCriteriaDetailsEditLog = $this->evaluationCriteriaDetailsEditLogRepo->find($evaluationCriteriaDetailsEditLog->id);
        $this->assertModelData($fakeEvaluationCriteriaDetailsEditLog, $dbEvaluationCriteriaDetailsEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->create();

        $resp = $this->evaluationCriteriaDetailsEditLogRepo->delete($evaluationCriteriaDetailsEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationCriteriaDetailsEditLog::find($evaluationCriteriaDetailsEditLog->id), 'EvaluationCriteriaDetailsEditLog should not exist in DB');
    }
}
