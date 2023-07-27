<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrEmpDepartments;

class HrEmpDepartmentsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_emp_departments', $hrEmpDepartments
        );

        $this->assertApiResponse($hrEmpDepartments);
    }

    /**
     * @test
     */
    public function test_read_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_emp_departments/'.$hrEmpDepartments->id
        );

        $this->assertApiResponse($hrEmpDepartments->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->create();
        $editedHrEmpDepartments = factory(HrEmpDepartments::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_emp_departments/'.$hrEmpDepartments->id,
            $editedHrEmpDepartments
        );

        $this->assertApiResponse($editedHrEmpDepartments);
    }

    /**
     * @test
     */
    public function test_delete_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_emp_departments/'.$hrEmpDepartments->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_emp_departments/'.$hrEmpDepartments->id
        );

        $this->response->assertStatus(404);
    }
}
