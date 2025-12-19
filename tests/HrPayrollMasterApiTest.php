<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrPayrollMaster;

class HrPayrollMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_payroll_masters', $hrPayrollMaster
        );

        $this->assertApiResponse($hrPayrollMaster);
    }

    /**
     * @test
     */
    public function test_read_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_payroll_masters/'.$hrPayrollMaster->id
        );

        $this->assertApiResponse($hrPayrollMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->create();
        $editedHrPayrollMaster = factory(HrPayrollMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_payroll_masters/'.$hrPayrollMaster->id,
            $editedHrPayrollMaster
        );

        $this->assertApiResponse($editedHrPayrollMaster);
    }

    /**
     * @test
     */
    public function test_delete_hr_payroll_master()
    {
        $hrPayrollMaster = factory(HrPayrollMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_payroll_masters/'.$hrPayrollMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_payroll_masters/'.$hrPayrollMaster->id
        );

        $this->response->assertStatus(404);
    }
}
