<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomUserReportSummarize;

class CustomUserReportSummarizeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_user_report_summarizes', $customUserReportSummarize
        );

        $this->assertApiResponse($customUserReportSummarize);
    }

    /**
     * @test
     */
    public function test_read_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_user_report_summarizes/'.$customUserReportSummarize->id
        );

        $this->assertApiResponse($customUserReportSummarize->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->create();
        $editedCustomUserReportSummarize = factory(CustomUserReportSummarize::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_user_report_summarizes/'.$customUserReportSummarize->id,
            $editedCustomUserReportSummarize
        );

        $this->assertApiResponse($editedCustomUserReportSummarize);
    }

    /**
     * @test
     */
    public function test_delete_custom_user_report_summarize()
    {
        $customUserReportSummarize = factory(CustomUserReportSummarize::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_user_report_summarizes/'.$customUserReportSummarize->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_user_report_summarizes/'.$customUserReportSummarize->id
        );

        $this->response->assertStatus(404);
    }
}
