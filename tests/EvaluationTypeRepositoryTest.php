<?php namespace Tests\Repositories;

use App\Models\EvaluationType;
use App\Repositories\EvaluationTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class EvaluationTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var EvaluationTypeRepository
     */
    protected $evaluationTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->evaluationTypeRepo = \App::make(EvaluationTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->make()->toArray();

        $createdEvaluationType = $this->evaluationTypeRepo->create($evaluationType);

        $createdEvaluationType = $createdEvaluationType->toArray();
        $this->assertArrayHasKey('id', $createdEvaluationType);
        $this->assertNotNull($createdEvaluationType['id'], 'Created EvaluationType must have id specified');
        $this->assertNotNull(EvaluationType::find($createdEvaluationType['id']), 'EvaluationType with given id must be in DB');
        $this->assertModelData($evaluationType, $createdEvaluationType);
    }

    /**
     * @test read
     */
    public function test_read_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->create();

        $dbEvaluationType = $this->evaluationTypeRepo->find($evaluationType->id);

        $dbEvaluationType = $dbEvaluationType->toArray();
        $this->assertModelData($evaluationType->toArray(), $dbEvaluationType);
    }

    /**
     * @test update
     */
    public function test_update_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->create();
        $fakeEvaluationType = factory(EvaluationType::class)->make()->toArray();

        $updatedEvaluationType = $this->evaluationTypeRepo->update($fakeEvaluationType, $evaluationType->id);

        $this->assertModelData($fakeEvaluationType, $updatedEvaluationType->toArray());
        $dbEvaluationType = $this->evaluationTypeRepo->find($evaluationType->id);
        $this->assertModelData($fakeEvaluationType, $dbEvaluationType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_evaluation_type()
    {
        $evaluationType = factory(EvaluationType::class)->create();

        $resp = $this->evaluationTypeRepo->delete($evaluationType->id);

        $this->assertTrue($resp);
        $this->assertNull(EvaluationType::find($evaluationType->id), 'EvaluationType should not exist in DB');
    }
}
