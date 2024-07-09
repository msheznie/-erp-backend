<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationTemplateSection;

class EvaluationTemplateSectionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_template_sections', $evaluationTemplateSection
        );

        $this->assertApiResponse($evaluationTemplateSection);
    }

    /**
     * @test
     */
    public function test_read_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_template_sections/'.$evaluationTemplateSection->id
        );

        $this->assertApiResponse($evaluationTemplateSection->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->create();
        $editedEvaluationTemplateSection = factory(EvaluationTemplateSection::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_template_sections/'.$evaluationTemplateSection->id,
            $editedEvaluationTemplateSection
        );

        $this->assertApiResponse($editedEvaluationTemplateSection);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_template_sections/'.$evaluationTemplateSection->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_template_sections/'.$evaluationTemplateSection->id
        );

        $this->response->assertStatus(404);
    }
}
