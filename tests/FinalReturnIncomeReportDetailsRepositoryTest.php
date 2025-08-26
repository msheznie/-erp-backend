<?php namespace Tests\Repositories;

use App\Models\FinalReturnIncomeReportDetails;
use App\Repositories\FinalReturnIncomeReportDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinalReturnIncomeReportDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinalReturnIncomeReportDetailsRepository
     */
    protected $finalReturnIncomeReportDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->finalReturnIncomeReportDetailsRepo = \App::make(FinalReturnIncomeReportDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->make()->toArray();

        $createdFinalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepo->create($finalReturnIncomeReportDetails);

        $createdFinalReturnIncomeReportDetails = $createdFinalReturnIncomeReportDetails->toArray();
        $this->assertArrayHasKey('id', $createdFinalReturnIncomeReportDetails);
        $this->assertNotNull($createdFinalReturnIncomeReportDetails['id'], 'Created FinalReturnIncomeReportDetails must have id specified');
        $this->assertNotNull(FinalReturnIncomeReportDetails::find($createdFinalReturnIncomeReportDetails['id']), 'FinalReturnIncomeReportDetails with given id must be in DB');
        $this->assertModelData($finalReturnIncomeReportDetails, $createdFinalReturnIncomeReportDetails);
    }

    /**
     * @test read
     */
    public function test_read_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->create();

        $dbFinalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepo->find($finalReturnIncomeReportDetails->id);

        $dbFinalReturnIncomeReportDetails = $dbFinalReturnIncomeReportDetails->toArray();
        $this->assertModelData($finalReturnIncomeReportDetails->toArray(), $dbFinalReturnIncomeReportDetails);
    }

    /**
     * @test update
     */
    public function test_update_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->create();
        $fakeFinalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->make()->toArray();

        $updatedFinalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepo->update($fakeFinalReturnIncomeReportDetails, $finalReturnIncomeReportDetails->id);

        $this->assertModelData($fakeFinalReturnIncomeReportDetails, $updatedFinalReturnIncomeReportDetails->toArray());
        $dbFinalReturnIncomeReportDetails = $this->finalReturnIncomeReportDetailsRepo->find($finalReturnIncomeReportDetails->id);
        $this->assertModelData($fakeFinalReturnIncomeReportDetails, $dbFinalReturnIncomeReportDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->create();

        $resp = $this->finalReturnIncomeReportDetailsRepo->delete($finalReturnIncomeReportDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(FinalReturnIncomeReportDetails::find($finalReturnIncomeReportDetails->id), 'FinalReturnIncomeReportDetails should not exist in DB');
    }
}
