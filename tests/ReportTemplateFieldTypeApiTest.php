<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateFieldTypeApiTest extends TestCase
{
    use MakeReportTemplateFieldTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->fakeReportTemplateFieldTypeData();
        $this->json('POST', '/api/v1/reportTemplateFieldTypes', $reportTemplateFieldType);

        $this->assertApiResponse($reportTemplateFieldType);
    }

    /**
     * @test
     */
    public function testReadReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->makeReportTemplateFieldType();
        $this->json('GET', '/api/v1/reportTemplateFieldTypes/'.$reportTemplateFieldType->id);

        $this->assertApiResponse($reportTemplateFieldType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->makeReportTemplateFieldType();
        $editedReportTemplateFieldType = $this->fakeReportTemplateFieldTypeData();

        $this->json('PUT', '/api/v1/reportTemplateFieldTypes/'.$reportTemplateFieldType->id, $editedReportTemplateFieldType);

        $this->assertApiResponse($editedReportTemplateFieldType);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateFieldType()
    {
        $reportTemplateFieldType = $this->makeReportTemplateFieldType();
        $this->json('DELETE', '/api/v1/reportTemplateFieldTypes/'.$reportTemplateFieldType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateFieldTypes/'.$reportTemplateFieldType->id);

        $this->assertResponseStatus(404);
    }
}
