<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomReportEmployees;

class CustomReportEmployeesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_report_employees', $customReportEmployees
        );

        $this->assertApiResponse($customReportEmployees);
    }

    /**
     * @test
     */
    public function test_read_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_report_employees/'.$customReportEmployees->id
        );

        $this->assertApiResponse($customReportEmployees->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->create();
        $editedCustomReportEmployees = factory(CustomReportEmployees::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_report_employees/'.$customReportEmployees->id,
            $editedCustomReportEmployees
        );

        $this->assertApiResponse($editedCustomReportEmployees);
    }

    /**
     * @test
     */
    public function test_delete_custom_report_employees()
    {
        $customReportEmployees = factory(CustomReportEmployees::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_report_employees/'.$customReportEmployees->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_report_employees/'.$customReportEmployees->id
        );

        $this->response->assertStatus(404);
    }
}
