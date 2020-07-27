<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomUserReports;

class CustomUserReportsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_user_reports', $customUserReports
        );

        $this->assertApiResponse($customUserReports);
    }

    /**
     * @test
     */
    public function test_read_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_user_reports/'.$customUserReports->id
        );

        $this->assertApiResponse($customUserReports->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->create();
        $editedCustomUserReports = factory(CustomUserReports::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_user_reports/'.$customUserReports->id,
            $editedCustomUserReports
        );

        $this->assertApiResponse($editedCustomUserReports);
    }

    /**
     * @test
     */
    public function test_delete_custom_user_reports()
    {
        $customUserReports = factory(CustomUserReports::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_user_reports/'.$customUserReports->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_user_reports/'.$customUserReports->id
        );

        $this->response->assertStatus(404);
    }
}
