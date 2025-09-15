<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ReportCustomColumnTranslations;

class ReportCustomColumnTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/report_custom_column_translations', $reportCustomColumnTranslations
        );

        $this->assertApiResponse($reportCustomColumnTranslations);
    }

    /**
     * @test
     */
    public function test_read_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/report_custom_column_translations/'.$reportCustomColumnTranslations->id
        );

        $this->assertApiResponse($reportCustomColumnTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->create();
        $editedReportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/report_custom_column_translations/'.$reportCustomColumnTranslations->id,
            $editedReportCustomColumnTranslations
        );

        $this->assertApiResponse($editedReportCustomColumnTranslations);
    }

    /**
     * @test
     */
    public function test_delete_report_custom_column_translations()
    {
        $reportCustomColumnTranslations = factory(ReportCustomColumnTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/report_custom_column_translations/'.$reportCustomColumnTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/report_custom_column_translations/'.$reportCustomColumnTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
