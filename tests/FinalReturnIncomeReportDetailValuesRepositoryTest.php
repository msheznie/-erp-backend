<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeReportDetailValues;
use App\Repositories\FinalReturnIncomeReportDetailValuesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeReportDetailValuesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeReportDetailValuesRepository
     */
    protected $finalReturnIncomeReportDetailValuesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeReportDetailValuesRepo = \App::make(FinalReturnIncomeReportDetailValuesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->make()->toArray();

        $createdFinalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepo->create($finalReturnIncomeReportDetailValues);

        $createdFinalReturnIncomeReportDetailValues = $createdFinalReturnIncomeReportDetailValues->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeReportDetailValues);
        $this->assertNotNull($createdFinalReturnIncomeReportDetailValues['id'], 'Created FinalReturnIncomeReportDetailValues must have id specified');
        $this->assertNotNull(FinalReturnIncomeReportDetailValues::find($createdFinalReturnIncomeReportDetailValues['id']), 'FinalReturnIncomeReportDetailValues with given id must be in DB');
        $this->assertModelData($finalReturnIncomeReportDetailValues, $createdFinalReturnIncomeReportDetailValues);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->create();

        $dbFinalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepo->find($finalReturnIncomeReportDetailValues->id);

        $dbFinalReturnIncomeReportDetailValues = $dbFinalReturnIncomeReportDetailValues->toArray();
        $this->assertModelData($finalReturnIncomeReportDetailValues->toArray(), $dbFinalReturnIncomeReportDetailValues);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->create();
        $fakeFinalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->make()->toArray();

        $updatedFinalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepo->update($fakeFinalReturnIncomeReportDetailValues, $finalReturnIncomeReportDetailValues->id);

        $this->assertModelData($fakeFinalReturnIncomeReportDetailValues, $updatedFinalReturnIncomeReportDetailValues->toArray());
        $dbFinalReturnIncomeReportDetailValues = $this->finalReturnIncomeReportDetailValuesRepo->find($finalReturnIncomeReportDetailValues->id);
        $this->assertModelData($fakeFinalReturnIncomeReportDetailValues, $dbFinalReturnIncomeReportDetailValues->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->create();

        $resp = $this->finalReturnIncomeReportDetailValuesRepo->delete($finalReturnIncomeReportDetailValues->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeReportDetailValues::find($finalReturnIncomeReportDetailValues->id), 'FinalReturnIncomeReportDetailValues should not exist in DB');
    }
}
