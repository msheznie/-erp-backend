<?php namespace Tests\Repositories;

use App\Models\EvaluationTemplateSectionLabel;
use App\Repositories\EvaluationTemplateSectionLabelRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationTemplateSectionLabelRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationTemplateSectionLabelRepository
     */
    protected $evaluationTemplateSectionLabelRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationTemplateSectionLabelRepo = \App::make(EvaluationTemplateSectionLabelRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->make()->toArray();

        $createdEvaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepo->create($evaluationTemplateSectionLabel);

        $createdEvaluationTemplateSectionLabel = $createdEvaluationTemplateSectionLabel->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationTemplateSectionLabel);
        $this->assertNotNull($createdEvaluationTemplateSectionLabel['id'], 'Created EvaluationTemplateSectionLabel must have id specified');
        $this->assertNotNull(EvaluationTemplateSectionLabel::find($createdEvaluationTemplateSectionLabel['id']), 'EvaluationTemplateSectionLabel with given id must be in DB');
        $this->assertModelData($evaluationTemplateSectionLabel, $createdEvaluationTemplateSectionLabel);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->create();

        $dbEvaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepo->find($evaluationTemplateSectionLabel->id);

        $dbEvaluationTemplateSectionLabel = $dbEvaluationTemplateSectionLabel->toArray();
        $this->assertModelData($evaluationTemplateSectionLabel->toArray(), $dbEvaluationTemplateSectionLabel);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->create();
        $fakeEvaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->make()->toArray();

        $updatedEvaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepo->update($fakeEvaluationTemplateSectionLabel, $evaluationTemplateSectionLabel->id);

        $this->assertModelData($fakeEvaluationTemplateSectionLabel, $updatedEvaluationTemplateSectionLabel->toArray());
        $dbEvaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepo->find($evaluationTemplateSectionLabel->id);
        $this->assertModelData($fakeEvaluationTemplateSectionLabel, $dbEvaluationTemplateSectionLabel->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_template_section_label()
    {
        $evaluationTemplateSectionLabel = factory(EvaluationTemplateSectionLabel::class)->create();

        $resp = $this->evaluationTemplateSectionLabelRepo->delete($evaluationTemplateSectionLabel->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationTemplateSectionLabel::find($evaluationTemplateSectionLabel->id), 'EvaluationTemplateSectionLabel should not exist in DB');
    }
}
