<?php namespace Tests\Repositories;

use App\Models\EvaluationCriteriaType;
use App\Repositories\EvaluationCriteriaTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationCriteriaTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationCriteriaTypeRepository
     */
    protected $evaluationCriteriaTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationCriteriaTypeRepo = \App::make(EvaluationCriteriaTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->make()->toArray();

        $createdEvaluationCriteriaType = $this->evaluationCriteriaTypeRepo->create($evaluationCriteriaType);

        $createdEvaluationCriteriaType = $createdEvaluationCriteriaType->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationCriteriaType);
        $this->assertNotNull($createdEvaluationCriteriaType['id'], 'Created EvaluationCriteriaType must have id specified');
        $this->assertNotNull(EvaluationCriteriaType::find($createdEvaluationCriteriaType['id']), 'EvaluationCriteriaType with given id must be in DB');
        $this->assertModelData($evaluationCriteriaType, $createdEvaluationCriteriaType);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->create();

        $dbEvaluationCriteriaType = $this->evaluationCriteriaTypeRepo->find($evaluationCriteriaType->id);

        $dbEvaluationCriteriaType = $dbEvaluationCriteriaType->toArray();
        $this->assertModelData($evaluationCriteriaType->toArray(), $dbEvaluationCriteriaType);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->create();
        $fakeEvaluationCriteriaType = factory(EvaluationCriteriaType::class)->make()->toArray();

        $updatedEvaluationCriteriaType = $this->evaluationCriteriaTypeRepo->update($fakeEvaluationCriteriaType, $evaluationCriteriaType->id);

        $this->assertModelData($fakeEvaluationCriteriaType, $updatedEvaluationCriteriaType->toArray());
        $dbEvaluationCriteriaType = $this->evaluationCriteriaTypeRepo->find($evaluationCriteriaType->id);
        $this->assertModelData($fakeEvaluationCriteriaType, $dbEvaluationCriteriaType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_criteria_type()
    {
        $evaluationCriteriaType = factory(EvaluationCriteriaType::class)->create();

        $resp = $this->evaluationCriteriaTypeRepo->delete($evaluationCriteriaType->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationCriteriaType::find($evaluationCriteriaType->id), 'EvaluationCriteriaType should not exist in DB');
    }
}
