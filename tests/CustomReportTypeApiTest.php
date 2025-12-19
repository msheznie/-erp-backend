<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomReportType;

class CustomReportTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_report_types', $customReportType
        );

        $this->assertApiResponse($customReportType);
    }

    /**
     * @test
     */
    public function test_read_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_report_types/'.$customReportType->id
        );

        $this->assertApiResponse($customReportType->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->create();
        $editedCustomReportType = factory(CustomReportType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_report_types/'.$customReportType->id,
            $editedCustomReportType
        );

        $this->assertApiResponse($editedCustomReportType);
    }

    /**
     * @test
     */
    public function test_delete_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_report_types/'.$customReportType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_report_types/'.$customReportType->id
        );

        $this->response->assertStatus(404);
    }
}
