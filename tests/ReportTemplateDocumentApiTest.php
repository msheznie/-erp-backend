<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateDocumentApiTest extends TestCase
{
    use MakeReportTemplateDocumentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateDocument()
    {
        $reportTemplateDocument = $this->fakeReportTemplateDocumentData();
        $this->json('POST', '/api/v1/reportTemplateDocuments', $reportTemplateDocument);

        $this->assertApiResponse($reportTemplateDocument);
    }

    /**
     * @test
     */
    public function testReadReportTemplateDocument()
    {
        $reportTemplateDocument = $this->makeReportTemplateDocument();
        $this->json('GET', '/api/v1/reportTemplateDocuments/'.$reportTemplateDocument->id);

        $this->assertApiResponse($reportTemplateDocument->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateDocument()
    {
        $reportTemplateDocument = $this->makeReportTemplateDocument();
        $editedReportTemplateDocument = $this->fakeReportTemplateDocumentData();

        $this->json('PUT', '/api/v1/reportTemplateDocuments/'.$reportTemplateDocument->id, $editedReportTemplateDocument);

        $this->assertApiResponse($editedReportTemplateDocument);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateDocument()
    {
        $reportTemplateDocument = $this->makeReportTemplateDocument();
        $this->json('DELETE', '/api/v1/reportTemplateDocuments/'.$reportTemplateDocument->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateDocuments/'.$reportTemplateDocument->id);

        $this->assertResponseStatus(404);
    }
}
