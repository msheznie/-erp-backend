<?php namespace Tests\Repositories;

use App\Models\EvaluationTemplateSectionFormula;
use App\Repositories\EvaluationTemplateSectionFormulaRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationTemplateSectionFormulaRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationTemplateSectionFormulaRepository
     */
    protected $evaluationTemplateSectionFormulaRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationTemplateSectionFormulaRepo = \App::make(EvaluationTemplateSectionFormulaRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->make()->toArray();

        $createdEvaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepo->create($evaluationTemplateSectionFormula);

        $createdEvaluationTemplateSectionFormula = $createdEvaluationTemplateSectionFormula->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationTemplateSectionFormula);
        $this->assertNotNull($createdEvaluationTemplateSectionFormula['id'], 'Created EvaluationTemplateSectionFormula must have id specified');
        $this->assertNotNull(EvaluationTemplateSectionFormula::find($createdEvaluationTemplateSectionFormula['id']), 'EvaluationTemplateSectionFormula with given id must be in DB');
        $this->assertModelData($evaluationTemplateSectionFormula, $createdEvaluationTemplateSectionFormula);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->create();

        $dbEvaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepo->find($evaluationTemplateSectionFormula->id);

        $dbEvaluationTemplateSectionFormula = $dbEvaluationTemplateSectionFormula->toArray();
        $this->assertModelData($evaluationTemplateSectionFormula->toArray(), $dbEvaluationTemplateSectionFormula);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->create();
        $fakeEvaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->make()->toArray();

        $updatedEvaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepo->update($fakeEvaluationTemplateSectionFormula, $evaluationTemplateSectionFormula->id);

        $this->assertModelData($fakeEvaluationTemplateSectionFormula, $updatedEvaluationTemplateSectionFormula->toArray());
        $dbEvaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepo->find($evaluationTemplateSectionFormula->id);
        $this->assertModelData($fakeEvaluationTemplateSectionFormula, $dbEvaluationTemplateSectionFormula->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_template_section_formula()
    {
        $evaluationTemplateSectionFormula = factory(EvaluationTemplateSectionFormula::class)->create();

        $resp = $this->evaluationTemplateSectionFormulaRepo->delete($evaluationTemplateSectionFormula->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationTemplateSectionFormula::find($evaluationTemplateSectionFormula->id), 'EvaluationTemplateSectionFormula should not exist in DB');
    }
}
