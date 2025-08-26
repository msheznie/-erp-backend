<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinalReturnIncomeReports;

class FinalReturnIncomeReportsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/final_return_income_reports', $finalReturnIncomeReports
        );

        $this->assertApiResponse($finalReturnIncomeReports);
    }

    /**
     * @test
     */
    public function test_read_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/final_return_income_reports/'.$finalReturnIncomeReports->id
        );

        $this->assertApiResponse($finalReturnIncomeReports->toArray());
    }

    /**
     * @test
     */
    public function test_update_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->create();
        $editedFinalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/final_return_income_reports/'.$finalReturnIncomeReports->id,
            $editedFinalReturnIncomeReports
        );

        $this->assertApiResponse($editedFinalReturnIncomeReports);
    }

    /**
     * @test
     */
    public function test_delete_final_return_income_reports()
    {
        $finalReturnIncomeReports = factory(FinalReturnIncomeReports::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/final_return_income_reports/'.$finalReturnIncomeReports->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/final_return_income_reports/'.$finalReturnIncomeReports->id
        );

        $this->response->assertStatus(404);
    }
}
