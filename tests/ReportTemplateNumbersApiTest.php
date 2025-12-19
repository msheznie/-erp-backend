<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateNumbersApiTest extends TestCase
{
    use MakeReportTemplateNumbersTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->fakeReportTemplateNumbersData();
        $this->json('POST', '/api/v1/reportTemplateNumbers', $reportTemplateNumbers);

        $this->assertApiResponse($reportTemplateNumbers);
    }

    /**
     * @test
     */
    public function testReadReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->makeReportTemplateNumbers();
        $this->json('GET', '/api/v1/reportTemplateNumbers/'.$reportTemplateNumbers->id);

        $this->assertApiResponse($reportTemplateNumbers->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->makeReportTemplateNumbers();
        $editedReportTemplateNumbers = $this->fakeReportTemplateNumbersData();

        $this->json('PUT', '/api/v1/reportTemplateNumbers/'.$reportTemplateNumbers->id, $editedReportTemplateNumbers);

        $this->assertApiResponse($editedReportTemplateNumbers);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateNumbers()
    {
        $reportTemplateNumbers = $this->makeReportTemplateNumbers();
        $this->json('DELETE', '/api/v1/reportTemplateNumbers/'.$reportTemplateNumbers->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateNumbers/'.$reportTemplateNumbers->id);

        $this->assertResponseStatus(404);
    }
}
