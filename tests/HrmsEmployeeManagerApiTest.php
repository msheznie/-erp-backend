<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrmsEmployeeManager;

class HrmsEmployeeManagerApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hrms_employee_managers', $hrmsEmployeeManager
        );

        $this->assertApiResponse($hrmsEmployeeManager);
    }

    /**
     * @test
     */
    public function test_read_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hrms_employee_managers/'.$hrmsEmployeeManager->id
        );

        $this->assertApiResponse($hrmsEmployeeManager->toArray());
    }

    /**
     * @test
     */
    public function test_update_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->create();
        $editedHrmsEmployeeManager = factory(HrmsEmployeeManager::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hrms_employee_managers/'.$hrmsEmployeeManager->id,
            $editedHrmsEmployeeManager
        );

        $this->assertApiResponse($editedHrmsEmployeeManager);
    }

    /**
     * @test
     */
    public function test_delete_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hrms_employee_managers/'.$hrmsEmployeeManager->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hrms_employee_managers/'.$hrmsEmployeeManager->id
        );

        $this->response->assertStatus(404);
    }
}
