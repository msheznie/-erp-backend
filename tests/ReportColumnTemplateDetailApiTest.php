<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeReportColumnTemplateDetailTrait;
use Tests\ApiTestTrait;

class ReportColumnTemplateDetailApiTest extends TestCase
{
    use MakeReportColumnTemplateDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->fakeReportColumnTemplateDetailData();
        $this->response = $this->json('POST', '/api/reportColumnTemplateDetails', $reportColumnTemplateDetail);

        $this->assertApiResponse($reportColumnTemplateDetail);
    }

    /**
     * @test
     */
    public function test_read_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->makeReportColumnTemplateDetail();
        $this->response = $this->json('GET', '/api/reportColumnTemplateDetails/'.$reportColumnTemplateDetail->id);

        $this->assertApiResponse($reportColumnTemplateDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->makeReportColumnTemplateDetail();
        $editedReportColumnTemplateDetail = $this->fakeReportColumnTemplateDetailData();

        $this->response = $this->json('PUT', '/api/reportColumnTemplateDetails/'.$reportColumnTemplateDetail->id, $editedReportColumnTemplateDetail);

        $this->assertApiResponse($editedReportColumnTemplateDetail);
    }

    /**
     * @test
     */
    public function test_delete_report_column_template_detail()
    {
        $reportColumnTemplateDetail = $this->makeReportColumnTemplateDetail();
        $this->response = $this->json('DELETE', '/api/reportColumnTemplateDetails/'.$reportColumnTemplateDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/reportColumnTemplateDetails/'.$reportColumnTemplateDetail->id);

        $this->response->assertStatus(404);
    }
}
