<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\EmployeeMobileBillMaster;

class EmployeeMobileBillMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/employee_mobile_bill_masters', $employeeMobileBillMaster
        );

        $this->assertApiResponse($employeeMobileBillMaster);
    }

    /**
     * @test
     */
    public function test_read_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/employee_mobile_bill_masters/'.$employeeMobileBillMaster->id
        );

        $this->assertApiResponse($employeeMobileBillMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->create();
        $editedEmployeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/employee_mobile_bill_masters/'.$employeeMobileBillMaster->id,
            $editedEmployeeMobileBillMaster
        );

        $this->assertApiResponse($editedEmployeeMobileBillMaster);
    }

    /**
     * @test
     */
    public function test_delete_employee_mobile_bill_master()
    {
        $employeeMobileBillMaster = factory(EmployeeMobileBillMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/employee_mobile_bill_masters/'.$employeeMobileBillMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/employee_mobile_bill_masters/'.$employeeMobileBillMaster->id
        );

        $this->response->assertStatus(404);
    }
}
