<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateDetailsApiTest extends TestCase
{
    use MakeReportTemplateDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateDetails()
    {
        $reportTemplateDetails = $this->fakeReportTemplateDetailsData();
        $this->json('POST', '/api/v1/reportTemplateDetails', $reportTemplateDetails);

        $this->assertApiResponse($reportTemplateDetails);
    }

    /**
     * @test
     */
    public function testReadReportTemplateDetails()
    {
        $reportTemplateDetails = $this->makeReportTemplateDetails();
        $this->json('GET', '/api/v1/reportTemplateDetails/'.$reportTemplateDetails->id);

        $this->assertApiResponse($reportTemplateDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateDetails()
    {
        $reportTemplateDetails = $this->makeReportTemplateDetails();
        $editedReportTemplateDetails = $this->fakeReportTemplateDetailsData();

        $this->json('PUT', '/api/v1/reportTemplateDetails/'.$reportTemplateDetails->id, $editedReportTemplateDetails);

        $this->assertApiResponse($editedReportTemplateDetails);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateDetails()
    {
        $reportTemplateDetails = $this->makeReportTemplateDetails();
        $this->json('DELETE', '/api/v1/reportTemplateDetails/'.$reportTemplateDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateDetails/'.$reportTemplateDetails->id);

        $this->assertResponseStatus(404);
    }
}
