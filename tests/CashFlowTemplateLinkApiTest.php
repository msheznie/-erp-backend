<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CashFlowTemplateLink;

class CashFlowTemplateLinkApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/cash_flow_template_links', $cashFlowTemplateLink
        );

        $this->assertApiResponse($cashFlowTemplateLink);
    }

    /**
     * @test
     */
    public function test_read_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/cash_flow_template_links/'.$cashFlowTemplateLink->id
        );

        $this->assertApiResponse($cashFlowTemplateLink->toArray());
    }

    /**
     * @test
     */
    public function test_update_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->create();
        $editedCashFlowTemplateLink = factory(CashFlowTemplateLink::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/cash_flow_template_links/'.$cashFlowTemplateLink->id,
            $editedCashFlowTemplateLink
        );

        $this->assertApiResponse($editedCashFlowTemplateLink);
    }

    /**
     * @test
     */
    public function test_delete_cash_flow_template_link()
    {
        $cashFlowTemplateLink = factory(CashFlowTemplateLink::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/cash_flow_template_links/'.$cashFlowTemplateLink->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/cash_flow_template_links/'.$cashFlowTemplateLink->id
        );

        $this->response->assertStatus(404);
    }
}
