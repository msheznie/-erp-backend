<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationTemplateSectionLabel;

class EvaluationTemplateSectionLabelApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_template_section_labels', $evaluationTemplateSectionLabel
        );

        $this->assertApiResponse($evaluationTemplateSectionLabel);
    }

    /**
     * @test
     */
    public function test_read_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_template_section_labels/'.$evaluationTemplateSectionLabel->id
        );

        $this->assertApiResponse($evaluationTemplateSectionLabel->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->create();
        $editedEvaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_template_section_labels/'.$evaluationTemplateSectionLabel->id,
            $editedEvaluationTemplateSectionLabel
        );

        $this->assertApiResponse($editedEvaluationTemplateSectionLabel);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_template_section_labels/'.$evaluationTemplateSectionLabel->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_template_section_labels/'.$evaluationTemplateSectionLabel->id
        );

        $this->response->assertStatus(404);
    }
}
