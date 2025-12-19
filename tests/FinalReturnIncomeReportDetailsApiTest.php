<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeReportDetails;

class FinalReturnIncomeReportDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_report_details', $finalReturnIncomeReportDetails
        );

        $this->assertApiResponse($finalReturnIncomeReportDetails);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_report_details/'.$finalReturnIncomeReportDetails->id
        );

        $this->assertApiResponse($finalReturnIncomeReportDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->create();
        $editedFinalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_report_details/'.$finalReturnIncomeReportDetails->id,
            $editedFinalReturnIncomeReportDetails
        );

        $this->assertApiResponse($editedFinalReturnIncomeReportDetails);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_report_details()
    {
        $finalReturnIncomeReportDetails = factory(FinalReturnIncomeReportDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_report_details/'.$finalReturnIncomeReportDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_report_details/'.$finalReturnIncomeReportDetails->id
        );

        $this->response->assertStatus(404);
    }
}
