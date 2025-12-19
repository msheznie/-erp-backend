<?php namespace Tests\Repositories;

use App\Models\EvaluationCriteriaScoreConfig;
use App\Repositories\EvaluationCriteriaScoreConfigRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationCriteriaScoreConfigRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationCriteriaScoreConfigRepository
     */
    protected $evaluationCriteriaScoreConfigRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationCriteriaScoreConfigRepo = \App::make(EvaluationCriteriaScoreConfigRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->make()->toArray();

        $createdEvaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepo->create($evaluationCriteriaScoreConfig);

        $createdEvaluationCriteriaScoreConfig = $createdEvaluationCriteriaScoreConfig->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationCriteriaScoreConfig);
        $this->assertNotNull($createdEvaluationCriteriaScoreConfig['id'], 'Created EvaluationCriteriaScoreConfig must have id specified');
        $this->assertNotNull(EvaluationCriteriaScoreConfig::find($createdEvaluationCriteriaScoreConfig['id']), 'EvaluationCriteriaScoreConfig with given id must be in DB');
        $this->assertModelData($evaluationCriteriaScoreConfig, $createdEvaluationCriteriaScoreConfig);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->create();

        $dbEvaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepo->find($evaluationCriteriaScoreConfig->id);

        $dbEvaluationCriteriaScoreConfig = $dbEvaluationCriteriaScoreConfig->toArray();
        $this->assertModelData($evaluationCriteriaScoreConfig->toArray(), $dbEvaluationCriteriaScoreConfig);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->create();
        $fakeEvaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->make()->toArray();

        $updatedEvaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepo->update($fakeEvaluationCriteriaScoreConfig, $evaluationCriteriaScoreConfig->id);

        $this->assertModelData($fakeEvaluationCriteriaScoreConfig, $updatedEvaluationCriteriaScoreConfig->toArray());
        $dbEvaluationCriteriaScoreConfig = $this->evaluationCriteriaScoreConfigRepo->find($evaluationCriteriaScoreConfig->id);
        $this->assertModelData($fakeEvaluationCriteriaScoreConfig, $dbEvaluationCriteriaScoreConfig->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->create();

        $resp = $this->evaluationCriteriaScoreConfigRepo->delete($evaluationCriteriaScoreConfig->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationCriteriaScoreConfig::find($evaluationCriteriaScoreConfig->id), 'EvaluationCriteriaScoreConfig should not exist in DB');
    }
}
