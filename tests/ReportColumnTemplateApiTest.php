<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeReportColumnTemplateTrait;
use Tests\ApiTestTrait;

class ReportColumnTemplateApiTest extends TestCase
{
    use MakeReportColumnTemplateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_report_column_template()
    {
        $reportColumnTemplate = $this->fakeReportColumnTemplateData();
        $this->response = $this->json('POST', '/api/reportColumnTemplates', $reportColumnTemplate);

        $this->assertApiResponse($reportColumnTemplate);
    }

    /**
     * @test
     */
    public function test_read_report_column_template()
    {
        $reportColumnTemplate = $this->makeReportColumnTemplate();
        $this->response = $this->json('GET', '/api/reportColumnTemplates/'.$reportColumnTemplate->id);

        $this->assertApiResponse($reportColumnTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_report_column_template()
    {
        $reportColumnTemplate = $this->makeReportColumnTemplate();
        $editedReportColumnTemplate = $this->fakeReportColumnTemplateData();

        $this->response = $this->json('PUT', '/api/reportColumnTemplates/'.$reportColumnTemplate->id, $editedReportColumnTemplate);

        $this->assertApiResponse($editedReportColumnTemplate);
    }

    /**
     * @test
     */
    public function test_delete_report_column_template()
    {
        $reportColumnTemplate = $this->makeReportColumnTemplate();
        $this->response = $this->json('DELETE', '/api/reportColumnTemplates/'.$reportColumnTemplate->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/reportColumnTemplates/'.$reportColumnTemplate->id);

        $this->response->assertStatus(404);
    }
}
