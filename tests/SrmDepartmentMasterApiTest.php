<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrmDepartmentMaster;

class SrmDepartmentMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srm_department_masters', $srmDepartmentMaster
        );

        $this->assertApiResponse($srmDepartmentMaster);
    }

    /**
     * @test
     */
    public function test_read_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srm_department_masters/'.$srmDepartmentMaster->id
        );

        $this->assertApiResponse($srmDepartmentMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->create();
        $editedSrmDepartmentMaster = factory(SrmDepartmentMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srm_department_masters/'.$srmDepartmentMaster->id,
            $editedSrmDepartmentMaster
        );

        $this->assertApiResponse($editedSrmDepartmentMaster);
    }

    /**
     * @test
     */
    public function test_delete_srm_department_master()
    {
        $srmDepartmentMaster = factory(SrmDepartmentMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srm_department_masters/'.$srmDepartmentMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srm_department_masters/'.$srmDepartmentMaster->id
        );

        $this->response->assertStatus(404);
    }
}
