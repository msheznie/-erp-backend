<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateColumnsApiTest extends TestCase
{
    use MakeReportTemplateColumnsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateColumns()
    {
        $reportTemplateColumns = $this->fakeReportTemplateColumnsData();
        $this->json('POST', '/api/v1/reportTemplateColumns', $reportTemplateColumns);

        $this->assertApiResponse($reportTemplateColumns);
    }

    /**
     * @test
     */
    public function testReadReportTemplateColumns()
    {
        $reportTemplateColumns = $this->makeReportTemplateColumns();
        $this->json('GET', '/api/v1/reportTemplateColumns/'.$reportTemplateColumns->id);

        $this->assertApiResponse($reportTemplateColumns->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateColumns()
    {
        $reportTemplateColumns = $this->makeReportTemplateColumns();
        $editedReportTemplateColumns = $this->fakeReportTemplateColumnsData();

        $this->json('PUT', '/api/v1/reportTemplateColumns/'.$reportTemplateColumns->id, $editedReportTemplateColumns);

        $this->assertApiResponse($editedReportTemplateColumns);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateColumns()
    {
        $reportTemplateColumns = $this->makeReportTemplateColumns();
        $this->json('DELETE', '/api/v1/reportTemplateColumns/'.$reportTemplateColumns->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateColumns/'.$reportTemplateColumns->id);

        $this->assertResponseStatus(404);
    }
}
