<?php namespace Tests\Repositories;

use App\Models\EvaluationCriteriaDetails;
use App\Repositories\EvaluationCriteriaDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationCriteriaDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationCriteriaDetailsRepository
     */
    protected $evaluationCriteriaDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationCriteriaDetailsRepo = \App::make(EvaluationCriteriaDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->make()->toArray();

        $createdEvaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepo->create($evaluationCriteriaDetails);

        $createdEvaluationCriteriaDetails = $createdEvaluationCriteriaDetails->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationCriteriaDetails);
        $this->assertNotNull($createdEvaluationCriteriaDetails['id'], 'Created EvaluationCriteriaDetails must have id specified');
        $this->assertNotNull(EvaluationCriteriaDetails::find($createdEvaluationCriteriaDetails['id']), 'EvaluationCriteriaDetails with given id must be in DB');
        $this->assertModelData($evaluationCriteriaDetails, $createdEvaluationCriteriaDetails);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->create();

        $dbEvaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepo->find($evaluationCriteriaDetails->id);

        $dbEvaluationCriteriaDetails = $dbEvaluationCriteriaDetails->toArray();
        $this->assertModelData($evaluationCriteriaDetails->toArray(), $dbEvaluationCriteriaDetails);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->create();
        $fakeEvaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->make()->toArray();

        $updatedEvaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepo->update($fakeEvaluationCriteriaDetails, $evaluationCriteriaDetails->id);

        $this->assertModelData($fakeEvaluationCriteriaDetails, $updatedEvaluationCriteriaDetails->toArray());
        $dbEvaluationCriteriaDetails = $this->evaluationCriteriaDetailsRepo->find($evaluationCriteriaDetails->id);
        $this->assertModelData($fakeEvaluationCriteriaDetails, $dbEvaluationCriteriaDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_criteria_details()
    {
        $evaluationCriteriaDetails = factory(EvaluationCriteriaDetails::class)->create();

        $resp = $this->evaluationCriteriaDetailsRepo->delete($evaluationCriteriaDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationCriteriaDetails::find($evaluationCriteriaDetails->id), 'EvaluationCriteriaDetails should not exist in DB');
    }
}
