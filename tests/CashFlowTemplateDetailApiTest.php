<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CashFlowTemplateDetail;

class CashFlowTemplateDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cash_flow_template_details', $cashFlowTemplateDetail
        );

        $this->assertApiResponse($cashFlowTemplateDetail);
    }

    /**
     * @test
     */
    public function test_read_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cash_flow_template_details/'.$cashFlowTemplateDetail->id
        );

        $this->assertApiResponse($cashFlowTemplateDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->create();
        $editedCashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cash_flow_template_details/'.$cashFlowTemplateDetail->id,
            $editedCashFlowTemplateDetail
        );

        $this->assertApiResponse($editedCashFlowTemplateDetail);
    }

    /**
     * @test
     */
    public function test_delete_cash_flow_template_detail()
    {
        $cashFlowTemplateDetail = factory(CashFlowTemplateDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cash_flow_template_details/'.$cashFlowTemplateDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cash_flow_template_details/'.$cashFlowTemplateDetail->id
        );

        $this->response->assertStatus(404);
    }
}
