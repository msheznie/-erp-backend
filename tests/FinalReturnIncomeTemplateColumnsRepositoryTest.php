<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeTemplateColumns;
use App\Repositories\FinalReturnIncomeTemplateColumnsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeTemplateColumnsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeTemplateColumnsRepository
     */
    protected $finalReturnIncomeTemplateColumnsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeTemplateColumnsRepo = \App::make(FinalReturnIncomeTemplateColumnsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->make()->toArray();

        $createdFinalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepo->create($finalReturnIncomeTemplateColumns);

        $createdFinalReturnIncomeTemplateColumns = $createdFinalReturnIncomeTemplateColumns->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeTemplateColumns);
        $this->assertNotNull($createdFinalReturnIncomeTemplateColumns['id'], 'Created FinalReturnIncomeTemplateColumns must have id specified');
        $this->assertNotNull(FinalReturnIncomeTemplateColumns::find($createdFinalReturnIncomeTemplateColumns['id']), 'FinalReturnIncomeTemplateColumns with given id must be in DB');
        $this->assertModelData($finalReturnIncomeTemplateColumns, $createdFinalReturnIncomeTemplateColumns);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->create();

        $dbFinalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepo->find($finalReturnIncomeTemplateColumns->id);

        $dbFinalReturnIncomeTemplateColumns = $dbFinalReturnIncomeTemplateColumns->toArray();
        $this->assertModelData($finalReturnIncomeTemplateColumns->toArray(), $dbFinalReturnIncomeTemplateColumns);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->create();
        $fakeFinalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->make()->toArray();

        $updatedFinalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepo->update($fakeFinalReturnIncomeTemplateColumns, $finalReturnIncomeTemplateColumns->id);

        $this->assertModelData($fakeFinalReturnIncomeTemplateColumns, $updatedFinalReturnIncomeTemplateColumns->toArray());
        $dbFinalReturnIncomeTemplateColumns = $this->finalReturnIncomeTemplateColumnsRepo->find($finalReturnIncomeTemplateColumns->id);
        $this->assertModelData($fakeFinalReturnIncomeTemplateColumns, $dbFinalReturnIncomeTemplateColumns->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_template_columns()
    {
        $finalReturnIncomeTemplateColumns = factory(FinalReturnIncomeTemplateColumns::class)->create();

        $resp = $this->finalReturnIncomeTemplateColumnsRepo->delete($finalReturnIncomeTemplateColumns->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeTemplateColumns::find($finalReturnIncomeTemplateColumns->id), 'FinalReturnIncomeTemplateColumns should not exist in DB');
    }
}
