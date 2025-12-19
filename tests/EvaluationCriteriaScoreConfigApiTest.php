<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationCriteriaScoreConfig;

class EvaluationCriteriaScoreConfigApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_criteria_score_configs', $evaluationCriteriaScoreConfig
        );

        $this->assertApiResponse($evaluationCriteriaScoreConfig);
    }

    /**
     * @test
     */
    public function test_read_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_score_configs/'.$evaluationCriteriaScoreConfig->id
        );

        $this->assertApiResponse($evaluationCriteriaScoreConfig->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->create();
        $editedEvaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_criteria_score_configs/'.$evaluationCriteriaScoreConfig->id,
            $editedEvaluationCriteriaScoreConfig
        );

        $this->assertApiResponse($editedEvaluationCriteriaScoreConfig);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_criteria_score_config()
    {
        $evaluationCriteriaScoreConfig = factory(EvaluationCriteriaScoreConfig::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_criteria_score_configs/'.$evaluationCriteriaScoreConfig->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_score_configs/'.$evaluationCriteriaScoreConfig->id
        );

        $this->response->assertStatus(404);
    }
}
