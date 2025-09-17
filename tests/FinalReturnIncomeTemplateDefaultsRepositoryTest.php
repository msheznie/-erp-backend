<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeTemplateDefaults;
use App\Repositories\FinalReturnIncomeTemplateDefaultsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeTemplateDefaultsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeTemplateDefaultsRepository
     */
    protected $finalReturnIncomeTemplateDefaultsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeTemplateDefaultsRepo = \App::make(FinalReturnIncomeTemplateDefaultsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->make()->toArray();

        $createdFinalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepo->create($finalReturnIncomeTemplateDefaults);

        $createdFinalReturnIncomeTemplateDefaults = $createdFinalReturnIncomeTemplateDefaults->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeTemplateDefaults);
        $this->assertNotNull($createdFinalReturnIncomeTemplateDefaults['id'], 'Created FinalReturnIncomeTemplateDefaults must have id specified');
        $this->assertNotNull(FinalReturnIncomeTemplateDefaults::find($createdFinalReturnIncomeTemplateDefaults['id']), 'FinalReturnIncomeTemplateDefaults with given id must be in DB');
        $this->assertModelData($finalReturnIncomeTemplateDefaults, $createdFinalReturnIncomeTemplateDefaults);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->create();

        $dbFinalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepo->find($finalReturnIncomeTemplateDefaults->id);

        $dbFinalReturnIncomeTemplateDefaults = $dbFinalReturnIncomeTemplateDefaults->toArray();
        $this->assertModelData($finalReturnIncomeTemplateDefaults->toArray(), $dbFinalReturnIncomeTemplateDefaults);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->create();
        $fakeFinalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->make()->toArray();

        $updatedFinalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepo->update($fakeFinalReturnIncomeTemplateDefaults, $finalReturnIncomeTemplateDefaults->id);

        $this->assertModelData($fakeFinalReturnIncomeTemplateDefaults, $updatedFinalReturnIncomeTemplateDefaults->toArray());
        $dbFinalReturnIncomeTemplateDefaults = $this->finalReturnIncomeTemplateDefaultsRepo->find($finalReturnIncomeTemplateDefaults->id);
        $this->assertModelData($fakeFinalReturnIncomeTemplateDefaults, $dbFinalReturnIncomeTemplateDefaults->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_template_defaults()
    {
        $finalReturnIncomeTemplateDefaults = factory(FinalReturnIncomeTemplateDefaults::class)->create();

        $resp = $this->finalReturnIncomeTemplateDefaultsRepo->delete($finalReturnIncomeTemplateDefaults->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeTemplateDefaults::find($finalReturnIncomeTemplateDefaults->id), 'FinalReturnIncomeTemplateDefaults should not exist in DB');
    }
}
