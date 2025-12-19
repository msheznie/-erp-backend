<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpEmployeeDetails;

class SrpEmployeeDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_employee_details', $srpEmployeeDetails
        );

        $this->assertApiResponse($srpEmployeeDetails);
    }

    /**
     * @test
     */
    public function test_read_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_employee_details/'.$srpEmployeeDetails->id
        );

        $this->assertApiResponse($srpEmployeeDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->create();
        $editedSrpEmployeeDetails = factory(SrpEmployeeDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_employee_details/'.$srpEmployeeDetails->id,
            $editedSrpEmployeeDetails
        );

        $this->assertApiResponse($editedSrpEmployeeDetails);
    }

    /**
     * @test
     */
    public function test_delete_srp_employee_details()
    {
        $srpEmployeeDetails = factory(SrpEmployeeDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_employee_details/'.$srpEmployeeDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_employee_details/'.$srpEmployeeDetails->id
        );

        $this->response->assertStatus(404);
    }
}
