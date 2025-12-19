<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakePreDefinedReportTemplateTrait;
use Tests\ApiTestTrait;

class PreDefinedReportTemplateApiTest extends TestCase
{
    use MakePreDefinedReportTemplateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->fakePreDefinedReportTemplateData();
        $this->response = $this->json('POST', '/api/preDefinedReportTemplates', $preDefinedReportTemplate);

        $this->assertApiResponse($preDefinedReportTemplate);
    }

    /**
     * @test
     */
    public function test_read_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->makePreDefinedReportTemplate();
        $this->response = $this->json('GET', '/api/preDefinedReportTemplates/'.$preDefinedReportTemplate->id);

        $this->assertApiResponse($preDefinedReportTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->makePreDefinedReportTemplate();
        $editedPreDefinedReportTemplate = $this->fakePreDefinedReportTemplateData();

        $this->response = $this->json('PUT', '/api/preDefinedReportTemplates/'.$preDefinedReportTemplate->id, $editedPreDefinedReportTemplate);

        $this->assertApiResponse($editedPreDefinedReportTemplate);
    }

    /**
     * @test
     */
    public function test_delete_pre_defined_report_template()
    {
        $preDefinedReportTemplate = $this->makePreDefinedReportTemplate();
        $this->response = $this->json('DELETE', '/api/preDefinedReportTemplates/'.$preDefinedReportTemplate->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/preDefinedReportTemplates/'.$preDefinedReportTemplate->id);

        $this->response->assertStatus(404);
    }
}
