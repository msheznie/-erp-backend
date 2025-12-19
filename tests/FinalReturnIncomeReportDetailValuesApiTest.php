<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeReportDetailValues;

class FinalReturnIncomeReportDetailValuesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_report_detail_values', $finalReturnIncomeReportDetailValues
        );

        $this->assertApiResponse($finalReturnIncomeReportDetailValues);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_report_detail_values/'.$finalReturnIncomeReportDetailValues->id
        );

        $this->assertApiResponse($finalReturnIncomeReportDetailValues->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->create();
        $editedFinalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_report_detail_values/'.$finalReturnIncomeReportDetailValues->id,
            $editedFinalReturnIncomeReportDetailValues
        );

        $this->assertApiResponse($editedFinalReturnIncomeReportDetailValues);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_report_detail_values()
    {
        $finalReturnIncomeReportDetailValues = factory(FinalReturnIncomeReportDetailValues::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_report_detail_values/'.$finalReturnIncomeReportDetailValues->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_report_detail_values/'.$finalReturnIncomeReportDetailValues->id
        );

        $this->response->assertStatus(404);
    }
}
