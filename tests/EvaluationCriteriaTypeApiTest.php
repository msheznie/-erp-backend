<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationCriteriaType;

class EvaluationCriteriaTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_criteria_types', $evaluationCriteriaType
        );

        $this->assertApiResponse($evaluationCriteriaType);
    }

    /**
     * @test
     */
    public function test_read_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_types/'.$evaluationCriteriaType->id
        );

        $this->assertApiResponse($evaluationCriteriaType->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->create();
        $editedEvaluationCriteriaType = factory(EvaluationCriteriaType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_criteria_types/'.$evaluationCriteriaType->id,
            $editedEvaluationCriteriaType
        );

        $this->assertApiResponse($editedEvaluationCriteriaType);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_criteria_types/'.$evaluationCriteriaType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_types/'.$evaluationCriteriaType->id
        );

        $this->response->assertStatus(404);
    }
}
