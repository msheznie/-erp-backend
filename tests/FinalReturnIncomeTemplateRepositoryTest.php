<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeTemplate;
use App\Repositories\FinalReturnIncomeTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeTemplateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeTemplateRepository
     */
    protected $finalReturnIncomeTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeTemplateRepo = \App::make(FinalReturnIncomeTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->make()->toArray();

        $createdFinalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepo->create($finalReturnIncomeTemplate);

        $createdFinalReturnIncomeTemplate = $createdFinalReturnIncomeTemplate->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeTemplate);
        $this->assertNotNull($createdFinalReturnIncomeTemplate['id'], 'Created FinalReturnIncomeTemplate must have id specified');
        $this->assertNotNull(FinalReturnIncomeTemplate::find($createdFinalReturnIncomeTemplate['id']), 'FinalReturnIncomeTemplate with given id must be in DB');
        $this->assertModelData($finalReturnIncomeTemplate, $createdFinalReturnIncomeTemplate);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->create();

        $dbFinalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepo->find($finalReturnIncomeTemplate->id);

        $dbFinalReturnIncomeTemplate = $dbFinalReturnIncomeTemplate->toArray();
        $this->assertModelData($finalReturnIncomeTemplate->toArray(), $dbFinalReturnIncomeTemplate);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->create();
        $fakeFinalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->make()->toArray();

        $updatedFinalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepo->update($fakeFinalReturnIncomeTemplate, $finalReturnIncomeTemplate->id);

        $this->assertModelData($fakeFinalReturnIncomeTemplate, $updatedFinalReturnIncomeTemplate->toArray());
        $dbFinalReturnIncomeTemplate = $this->finalReturnIncomeTemplateRepo->find($finalReturnIncomeTemplate->id);
        $this->assertModelData($fakeFinalReturnIncomeTemplate, $dbFinalReturnIncomeTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_template()
    {
        $finalReturnIncomeTemplate = factory(FinalReturnIncomeTemplate::class)->create();

        $resp = $this->finalReturnIncomeTemplateRepo->delete($finalReturnIncomeTemplate->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeTemplate::find($finalReturnIncomeTemplate->id), 'FinalReturnIncomeTemplate should not exist in DB');
    }
}
