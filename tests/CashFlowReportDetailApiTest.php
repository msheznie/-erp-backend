<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CashFlowReportDetail;

class CashFlowReportDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cash_flow_report_details', $cashFlowReportDetail
        );

        $this->assertApiResponse($cashFlowReportDetail);
    }

    /**
     * @test
     */
    public function test_read_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cash_flow_report_details/'.$cashFlowReportDetail->id
        );

        $this->assertApiResponse($cashFlowReportDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->create();
        $editedCashFlowReportDetail = factory(CashFlowReportDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cash_flow_report_details/'.$cashFlowReportDetail->id,
            $editedCashFlowReportDetail
        );

        $this->assertApiResponse($editedCashFlowReportDetail);
    }

    /**
     * @test
     */
    public function test_delete_cash_flow_report_detail()
    {
        $cashFlowReportDetail = factory(CashFlowReportDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cash_flow_report_details/'.$cashFlowReportDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cash_flow_report_details/'.$cashFlowReportDetail->id
        );

        $this->response->assertStatus(404);
    }
}
