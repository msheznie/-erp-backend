<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateApiTest extends TestCase
{
    use MakeReportTemplateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplate()
    {
        $reportTemplate = $this->fakeReportTemplateData();
        $this->json('POST', '/api/v1/reportTemplates', $reportTemplate);

        $this->assertApiResponse($reportTemplate);
    }

    /**
     * @test
     */
    public function testReadReportTemplate()
    {
        $reportTemplate = $this->makeReportTemplate();
        $this->json('GET', '/api/v1/reportTemplates/'.$reportTemplate->id);

        $this->assertApiResponse($reportTemplate->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplate()
    {
        $reportTemplate = $this->makeReportTemplate();
        $editedReportTemplate = $this->fakeReportTemplateData();

        $this->json('PUT', '/api/v1/reportTemplates/'.$reportTemplate->id, $editedReportTemplate);

        $this->assertApiResponse($editedReportTemplate);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplate()
    {
        $reportTemplate = $this->makeReportTemplate();
        $this->json('DELETE', '/api/v1/reportTemplates/'.$reportTemplate->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplates/'.$reportTemplate->id);

        $this->assertResponseStatus(404);
    }
}
