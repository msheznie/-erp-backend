<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ReportTemplateEquity;

class ReportTemplateEquityApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/report_template_equities', $reportTemplateEquity
        );

        $this->assertApiResponse($reportTemplateEquity);
    }

    /**
     * @test
     */
    public function test_read_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/report_template_equities/'.$reportTemplateEquity->id
        );

        $this->assertApiResponse($reportTemplateEquity->toArray());
    }

    /**
     * @test
     */
    public function test_update_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->create();
        $editedReportTemplateEquity = factory(ReportTemplateEquity::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/report_template_equities/'.$reportTemplateEquity->id,
            $editedReportTemplateEquity
        );

        $this->assertApiResponse($editedReportTemplateEquity);
    }

    /**
     * @test
     */
    public function test_delete_report_template_equity()
    {
        $reportTemplateEquity = factory(ReportTemplateEquity::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/report_template_equities/'.$reportTemplateEquity->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/report_template_equities/'.$reportTemplateEquity->id
        );

        $this->response->assertStatus(404);
    }
}
