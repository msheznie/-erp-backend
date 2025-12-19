<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DepartmentMasterTranslations;

class DepartmentMasterTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/department_master_translations', $departmentMasterTranslations
        );

        $this->assertApiResponse($departmentMasterTranslations);
    }

    /**
     * @test
     */
    public function test_read_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/department_master_translations/'.$departmentMasterTranslations->id
        );

        $this->assertApiResponse($departmentMasterTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->create();
        $editedDepartmentMasterTranslations = factory(DepartmentMasterTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/department_master_translations/'.$departmentMasterTranslations->id,
            $editedDepartmentMasterTranslations
        );

        $this->assertApiResponse($editedDepartmentMasterTranslations);
    }

    /**
     * @test
     */
    public function test_delete_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/department_master_translations/'.$departmentMasterTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/department_master_translations/'.$departmentMasterTranslations->id
        );

        $this->response->assertStatus(404);
    }
}
