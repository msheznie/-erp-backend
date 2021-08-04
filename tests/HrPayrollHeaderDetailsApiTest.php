<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrPayrollHeaderDetails;

class HrPayrollHeaderDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_payroll_header_details', $hrPayrollHeaderDetails
        );

        $this->assertApiResponse($hrPayrollHeaderDetails);
    }

    /**
     * @test
     */
    public function test_read_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_payroll_header_details/'.$hrPayrollHeaderDetails->id
        );

        $this->assertApiResponse($hrPayrollHeaderDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->create();
        $editedHrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_payroll_header_details/'.$hrPayrollHeaderDetails->id,
            $editedHrPayrollHeaderDetails
        );

        $this->assertApiResponse($editedHrPayrollHeaderDetails);
    }

    /**
     * @test
     */
    public function test_delete_hr_payroll_header_details()
    {
        $hrPayrollHeaderDetails = factory(HrPayrollHeaderDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_payroll_header_details/'.$hrPayrollHeaderDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_payroll_header_details/'.$hrPayrollHeaderDetails->id
        );

        $this->response->assertStatus(404);
    }
}
