<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ReportTemplateColumnsTranslations;

class ReportTemplateColumnsTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/report_template_columns_translations', $reportTemplateColumnsTranslations
        );

        $this->assertApiResponse($reportTemplateColumnsTranslations);
    }

    /**
     * @test
     */
    public function test_read_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/report_template_columns_translations/'.$reportTemplateColumnsTranslations->id
        );

        $this->assertApiResponse($reportTemplateColumnsTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->create();
        $editedReportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/report_template_columns_translations/'.$reportTemplateColumnsTranslations->id,
            $editedReportTemplateColumnsTranslations
        );

        $this->assertApiResponse($editedReportTemplateColumnsTranslations);
    }

    /**
     * @test
     */
    public function test_delete_report_template_columns_translations()
    {
        $reportTemplateColumnsTranslations = factory(ReportTemplateColumnsTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/report_template_columns_translations/'.$reportTemplateColumnsTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/report_template_columns_translations/'.$reportTemplateColumnsTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
