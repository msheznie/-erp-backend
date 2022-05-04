<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationCriteriaDetails;

class EvaluationCriteriaDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_criteria_details', $evaluationCriteriaDetails
        );

        $this->assertApiResponse($evaluationCriteriaDetails);
    }

    /**
     * @test
     */
    public function test_read_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_details/'.$evaluationCriteriaDetails->id
        );

        $this->assertApiResponse($evaluationCriteriaDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->create();
        $editedEvaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_criteria_details/'.$evaluationCriteriaDetails->id,
            $editedEvaluationCriteriaDetails
        );

        $this->assertApiResponse($editedEvaluationCriteriaDetails);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_criteria_details/'.$evaluationCriteriaDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_criteria_details/'.$evaluationCriteriaDetails->id
        );

        $this->response->assertStatus(404);
    }
}
