<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EvaluationTemplateSectionFormula;

class EvaluationTemplateSectionFormulaApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/evaluation_template_section_formulas', $evaluationTemplateSectionFormula
        );

        $this->assertApiResponse($evaluationTemplateSectionFormula);
    }

    /**
     * @test
     */
    public function test_read_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/evaluation_template_section_formulas/'.$evaluationTemplateSectionFormula->id
        );

        $this->assertApiResponse($evaluationTemplateSectionFormula->toArray());
    }

    /**
     * @test
     */
    public function test_update_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->create();
        $editedEvaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/evaluation_template_section_formulas/'.$evaluationTemplateSectionFormula->id,
            $editedEvaluationTemplateSectionFormula
        );

        $this->assertApiResponse($editedEvaluationTemplateSectionFormula);
    }

    /**
     * @test
     */
    public function test_delete_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/evaluation_template_section_formulas/'.$evaluationTemplateSectionFormula->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/evaluation_template_section_formulas/'.$evaluationTemplateSectionFormula->id
        );

        $this->response->assertStatus(404);
    }
}
