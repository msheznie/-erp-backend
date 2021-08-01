<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrPayrollDetails;

class HrPayrollDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_payroll_details', $hrPayrollDetails
        );

        $this->assertApiResponse($hrPayrollDetails);
    }

    /**
     * @test
     */
    public function test_read_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_payroll_details/'.$hrPayrollDetails->id
        );

        $this->assertApiResponse($hrPayrollDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->create();
        $editedHrPayrollDetails = factory(HrPayrollDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_payroll_details/'.$hrPayrollDetails->id,
            $editedHrPayrollDetails
        );

        $this->assertApiResponse($editedHrPayrollDetails);
    }

    /**
     * @test
     */
    public function test_delete_hr_payroll_details()
    {
        $hrPayrollDetails = factory(HrPayrollDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_payroll_details/'.$hrPayrollDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_payroll_details/'.$hrPayrollDetails->id
        );

        $this->response->assertStatus(404);
    }
}
