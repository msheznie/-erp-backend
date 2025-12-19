<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeReports;
use App\Repositories\FinalReturnIncomeReportsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeReportsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeReportsRepository
     */
    protected $finalReturnIncomeReportsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeReportsRepo = \App::make(FinalReturnIncomeReportsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->make()->toArray();

        $createdFinalReturnIncomeReports = $this->finalReturnIncomeReportsRepo->create($finalReturnIncomeReports);

        $createdFinalReturnIncomeReports = $createdFinalReturnIncomeReports->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeReports);
        $this->assertNotNull($createdFinalReturnIncomeReports['id'], 'Created FinalReturnIncomeReports must have id specified');
        $this->assertNotNull(FinalReturnIncomeReports::find($createdFinalReturnIncomeReports['id']), 'FinalReturnIncomeReports with given id must be in DB');
        $this->assertModelData($finalReturnIncomeReports, $createdFinalReturnIncomeReports);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->create();

        $dbFinalReturnIncomeReports = $this->finalReturnIncomeReportsRepo->find($finalReturnIncomeReports->id);

        $dbFinalReturnIncomeReports = $dbFinalReturnIncomeReports->toArray();
        $this->assertModelData($finalReturnIncomeReports->toArray(), $dbFinalReturnIncomeReports);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->create();
        $fakeFinalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->make()->toArray();

        $updatedFinalReturnIncomeReports = $this->finalReturnIncomeReportsRepo->update($fakeFinalReturnIncomeReports, $finalReturnIncomeReports->id);

        $this->assertModelData($fakeFinalReturnIncomeReports, $updatedFinalReturnIncomeReports->toArray());
        $dbFinalReturnIncomeReports = $this->finalReturnIncomeReportsRepo->find($finalReturnIncomeReports->id);
        $this->assertModelData($fakeFinalReturnIncomeReports, $dbFinalReturnIncomeReports->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->create();

        $resp = $this->finalReturnIncomeReportsRepo->delete($finalReturnIncomeReports->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeReports::find($finalReturnIncomeReports->id), 'FinalReturnIncomeReports should not exist in DB');
    }
}
