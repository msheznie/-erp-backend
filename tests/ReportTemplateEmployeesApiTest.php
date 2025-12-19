<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTemplateEmployeesApiTest extends TestCase
{
    use MakeReportTemplateEmployeesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->fakeReportTemplateEmployeesData();
        $this->json('POST', '/api/v1/reportTemplateEmployees', $reportTemplateEmployees);

        $this->assertApiResponse($reportTemplateEmployees);
    }

    /**
     * @test
     */
    public function testReadReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->makeReportTemplateEmployees();
        $this->json('GET', '/api/v1/reportTemplateEmployees/'.$reportTemplateEmployees->id);

        $this->assertApiResponse($reportTemplateEmployees->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->makeReportTemplateEmployees();
        $editedReportTemplateEmployees = $this->fakeReportTemplateEmployeesData();

        $this->json('PUT', '/api/v1/reportTemplateEmployees/'.$reportTemplateEmployees->id, $editedReportTemplateEmployees);

        $this->assertApiResponse($editedReportTemplateEmployees);
    }

    /**
     * @test
     */
    public function testDeleteReportTemplateEmployees()
    {
        $reportTemplateEmployees = $this->makeReportTemplateEmployees();
        $this->json('DELETE', '/api/v1/reportTemplateEmployees/'.$reportTemplateEmployees->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTemplateEmployees/'.$reportTemplateEmployees->id);

        $this->assertResponseStatus(404);
    }
}
