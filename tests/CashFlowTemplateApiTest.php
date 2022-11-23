<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CashFlowTemplate;

class CashFlowTemplateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cash_flow_templates', $cashFlowTemplate
        );

        $this->assertApiResponse($cashFlowTemplate);
    }

    /**
     * @test
     */
    public function test_read_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cash_flow_templates/'.$cashFlowTemplate->id
        );

        $this->assertApiResponse($cashFlowTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->create();
        $editedCashFlowTemplate = factory(CashFlowTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cash_flow_templates/'.$cashFlowTemplate->id,
            $editedCashFlowTemplate
        );

        $this->assertApiResponse($editedCashFlowTemplate);
    }

    /**
     * @test
     */
    public function test_delete_cash_flow_template()
    {
        $cashFlowTemplate = factory(CashFlowTemplate::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cash_flow_templates/'.$cashFlowTemplate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cash_flow_templates/'.$cashFlowTemplate->id
        );

        $this->response->assertStatus(404);
    }
}
