<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateLinksApiTest extends TestCase
{
    use MakeReportTemplateLinksTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateLinks()
    {
        $reportTemplateLinks = $this->fakeReportTemplateLinksData();
        $this->json('POST', '/api/v1/reportTemplateLinks', $reportTemplateLinks);

        $this->assertApiResponse($reportTemplateLinks);
    }

    /**
     * @test
     */
    public function testReadReportTemplateLinks()
    {
        $reportTemplateLinks = $this->makeReportTemplateLinks();
        $this->json('GET', '/api/v1/reportTemplateLinks/'.$reportTemplateLinks->id);

        $this->assertApiResponse($reportTemplateLinks->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateLinks()
    {
        $reportTemplateLinks = $this->makeReportTemplateLinks();
        $editedReportTemplateLinks = $this->fakeReportTemplateLinksData();

        $this->json('PUT', '/api/v1/reportTemplateLinks/'.$reportTemplateLinks->id, $editedReportTemplateLinks);

        $this->assertApiResponse($editedReportTemplateLinks);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateLinks()
    {
        $reportTemplateLinks = $this->makeReportTemplateLinks();
        $this->json('DELETE', '/api/v1/reportTemplateLinks/'.$reportTemplateLinks->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateLinks/'.$reportTemplateLinks->id);

        $this->assertResponseStatus(404);
    }
}
