<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrDepartmentMaster;

class HrDepartmentMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hr_department_masters', $hrDepartmentMaster
        );

        $this->assertApiResponse($hrDepartmentMaster);
    }

    /**
     * @test
     */
    public function test_read_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hr_department_masters/'.$hrDepartmentMaster->id
        );

        $this->assertApiResponse($hrDepartmentMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->create();
        $editedHrDepartmentMaster = factory(HrDepartmentMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hr_department_masters/'.$hrDepartmentMaster->id,
            $editedHrDepartmentMaster
        );

        $this->assertApiResponse($editedHrDepartmentMaster);
    }

    /**
     * @test
     */
    public function test_delete_hr_department_master()
    {
        $hrDepartmentMaster = factory(HrDepartmentMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hr_department_masters/'.$hrDepartmentMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hr_department_masters/'.$hrDepartmentMaster->id
        );

        $this->response->assertStatus(404);
    }
}
