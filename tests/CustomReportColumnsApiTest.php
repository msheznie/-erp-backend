<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomReportColumns;

class CustomReportColumnsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_report_columns', $customReportColumns
        );

        $this->assertApiResponse($customReportColumns);
    }

    /**
     * @test
     */
    public function test_read_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_report_columns/'.$customReportColumns->id
        );

        $this->assertApiResponse($customReportColumns->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->create();
        $editedCustomReportColumns = factory(CustomReportColumns::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_report_columns/'.$customReportColumns->id,
            $editedCustomReportColumns
        );

        $this->assertApiResponse($editedCustomReportColumns);
    }

    /**
     * @test
     */
    public function test_delete_custom_report_columns()
    {
        $customReportColumns = factory(CustomReportColumns::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_report_columns/'.$customReportColumns->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_report_columns/'.$customReportColumns->id
        );

        $this->response->assertStatus(404);
    }
}
