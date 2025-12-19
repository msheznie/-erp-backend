<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ReportCustomColumn;

class ReportCustomColumnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/report_custom_columns', $reportCustomColumn
        );

        $this->assertApiResponse($reportCustomColumn);
    }

    /**
     * @test
     */
    public function test_read_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/report_custom_columns/'.$reportCustomColumn->id
        );

        $this->assertApiResponse($reportCustomColumn->toArray());
    }

    /**
     * @test
     */
    public function test_update_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->create();
        $editedReportCustomColumn = factory(ReportCustomColumn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/report_custom_columns/'.$reportCustomColumn->id,
            $editedReportCustomColumn
        );

        $this->assertApiResponse($editedReportCustomColumn);
    }

    /**
     * @test
     */
    public function test_delete_report_custom_column()
    {
        $reportCustomColumn = factory(ReportCustomColumn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/report_custom_columns/'.$reportCustomColumn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/report_custom_columns/'.$reportCustomColumn->id
        );

        $this->response->assertStatus(404);
    }
}
