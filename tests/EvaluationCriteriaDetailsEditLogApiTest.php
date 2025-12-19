<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationCriteriaDetailsEditLog;

class EvaluationCriteriaDetailsEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_criteria_details_edit_logs', $evaluationCriteriaDetailsEditLog
        );

        $this->assertApiResponse($evaluationCriteriaDetailsEditLog);
    }

    /**
     * @test
     */
    public function test_read_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_details_edit_logs/'.$evaluationCriteriaDetailsEditLog->id
        );

        $this->assertApiResponse($evaluationCriteriaDetailsEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->create();
        $editedEvaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_criteria_details_edit_logs/'.$evaluationCriteriaDetailsEditLog->id,
            $editedEvaluationCriteriaDetailsEditLog
        );

        $this->assertApiResponse($editedEvaluationCriteriaDetailsEditLog);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_criteria_details_edit_log()
    {
        $evaluationCriteriaDetailsEditLog = factory(EvaluationCriteriaDetailsEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_criteria_details_edit_logs/'.$evaluationCriteriaDetailsEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_details_edit_logs/'.$evaluationCriteriaDetailsEditLog->id
        );

        $this->response->assertStatus(404);
    }
}
