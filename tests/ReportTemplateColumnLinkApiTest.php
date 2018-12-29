<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateColumnLinkApiTest extends TestCase
{
    use MakeReportTemplateColumnLinkTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->fakeReportTemplateColumnLinkData();
        $this->json('POST', '/api/v1/reportTemplateColumnLinks', $reportTemplateColumnLink);

        $this->assertApiResponse($reportTemplateColumnLink);
    }

    /**
     * @test
     */
    public function testReadReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->makeReportTemplateColumnLink();
        $this->json('GET', '/api/v1/reportTemplateColumnLinks/'.$reportTemplateColumnLink->id);

        $this->assertApiResponse($reportTemplateColumnLink->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->makeReportTemplateColumnLink();
        $editedReportTemplateColumnLink = $this->fakeReportTemplateColumnLinkData();

        $this->json('PUT', '/api/v1/reportTemplateColumnLinks/'.$reportTemplateColumnLink->id, $editedReportTemplateColumnLink);

        $this->assertApiResponse($editedReportTemplateColumnLink);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateColumnLink()
    {
        $reportTemplateColumnLink = $this->makeReportTemplateColumnLink();
        $this->json('DELETE', '/api/v1/reportTemplateColumnLinks/'.$reportTemplateColumnLink->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateColumnLinks/'.$reportTemplateColumnLink->id);

        $this->assertResponseStatus(404);
    }
}
