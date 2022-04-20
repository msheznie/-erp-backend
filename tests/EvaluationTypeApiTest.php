<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationType;

class EvaluationTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_types', $evaluationType
        );

        $this->assertApiResponse($evaluationType);
    }

    /**
     * @test
     */
    public function test_read_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_types/'.$evaluationType->id
        );

        $this->assertApiResponse($evaluationType->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->create();
        $editedEvaluationType = factory(EvaluationType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_types/'.$evaluationType->id,
            $editedEvaluationType
        );

        $this->assertApiResponse($editedEvaluationType);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_types/'.$evaluationType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_types/'.$evaluationType->id
        );

        $this->response->assertStatus(404);
    }
}
