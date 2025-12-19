<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomUserReportColumns;

class CustomUserReportColumnsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_user_report_columns', $customUserReportColumns
        );

        $this->assertApiResponse($customUserReportColumns);
    }

    /**
     * @test
     */
    public function test_read_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_user_report_columns/'.$customUserReportColumns->id
        );

        $this->assertApiResponse($customUserReportColumns->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->create();
        $editedCustomUserReportColumns = factory(CustomUserReportColumns::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_user_report_columns/'.$customUserReportColumns->id,
            $editedCustomUserReportColumns
        );

        $this->assertApiResponse($editedCustomUserReportColumns);
    }

    /**
     * @test
     */
    public function test_delete_custom_user_report_columns()
    {
        $customUserReportColumns = factory(CustomUserReportColumns::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_user_report_columns/'.$customUserReportColumns->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_user_report_columns/'.$customUserReportColumns->id
        );

        $this->response->assertStatus(404);
    }
}
