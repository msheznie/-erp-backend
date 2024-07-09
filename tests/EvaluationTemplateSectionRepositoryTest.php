<?php namespace Tests\Repositories;

use App\Models\EvaluationTemplateSection;
use App\Repositories\EvaluationTemplateSectionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationTemplateSectionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationTemplateSectionRepository
     */
    protected $evaluationTemplateSectionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationTemplateSectionRepo = \App::make(EvaluationTemplateSectionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->make()->toArray();

        $createdEvaluationTemplateSection = $this->evaluationTemplateSectionRepo->create($evaluationTemplateSection);

        $createdEvaluationTemplateSection = $createdEvaluationTemplateSection->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationTemplateSection);
        $this->assertNotNull($createdEvaluationTemplateSection['id'], 'Created EvaluationTemplateSection must have id specified');
        $this->assertNotNull(EvaluationTemplateSection::find($createdEvaluationTemplateSection['id']), 'EvaluationTemplateSection with given id must be in DB');
        $this->assertModelData($evaluationTemplateSection, $createdEvaluationTemplateSection);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->create();

        $dbEvaluationTemplateSection = $this->evaluationTemplateSectionRepo->find($evaluationTemplateSection->id);

        $dbEvaluationTemplateSection = $dbEvaluationTemplateSection->toArray();
        $this->assertModelData($evaluationTemplateSection->toArray(), $dbEvaluationTemplateSection);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->create();
        $fakeEvaluationTemplateSection = factory(EvaluationTemplateSection::class)->make()->toArray();

        $updatedEvaluationTemplateSection = $this->evaluationTemplateSectionRepo->update($fakeEvaluationTemplateSection, $evaluationTemplateSection->id);

        $this->assertModelData($fakeEvaluationTemplateSection, $updatedEvaluationTemplateSection->toArray());
        $dbEvaluationTemplateSection = $this->evaluationTemplateSectionRepo->find($evaluationTemplateSection->id);
        $this->assertModelData($fakeEvaluationTemplateSection, $dbEvaluationTemplateSection->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_template_section()
    {
        $evaluationTemplateSection = factory(EvaluationTemplateSection::class)->create();

        $resp = $this->evaluationTemplateSectionRepo->delete($evaluationTemplateSection->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationTemplateSection::find($evaluationTemplateSection->id), 'EvaluationTemplateSection should not exist in DB');
    }
}
