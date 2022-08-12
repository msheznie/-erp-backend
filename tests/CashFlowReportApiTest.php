<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CashFlowReport;

class CashFlowReportApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cash_flow_reports', $cashFlowReport
        );

        $this->assertApiResponse($cashFlowReport);
    }

    /**
     * @test
     */
    public function test_read_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cash_flow_reports/'.$cashFlowReport->id
        );

        $this->assertApiResponse($cashFlowReport->toArray());
    }

    /**
     * @test
     */
    public function test_update_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->create();
        $editedCashFlowReport = factory(CashFlowReport::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cash_flow_reports/'.$cashFlowReport->id,
            $editedCashFlowReport
        );

        $this->assertApiResponse($editedCashFlowReport);
    }

    /**
     * @test
     */
    public function test_delete_cash_flow_report()
    {
        $cashFlowReport = factory(CashFlowReport::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cash_flow_reports/'.$cashFlowReport->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cash_flow_reports/'.$cashFlowReport->id
        );

        $this->response->assertStatus(404);
    }
}
