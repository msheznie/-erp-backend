<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpProjectMaster;

class ErpProjectMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_project_masters', $erpProjectMaster
        );

        $this->assertApiResponse($erpProjectMaster);
    }

    /**
     * @test
     */
    public function test_read_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_project_masters/'.$erpProjectMaster->id
        );

        $this->assertApiResponse($erpProjectMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->create();
        $editedErpProjectMaster = factory(ErpProjectMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_project_masters/'.$erpProjectMaster->id,
            $editedErpProjectMaster
        );

        $this->assertApiResponse($editedErpProjectMaster);
    }

    /**
     * @test
     */
    public function test_delete_erp_project_master()
    {
        $erpProjectMaster = factory(ErpProjectMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_project_masters/'.$erpProjectMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_project_masters/'.$erpProjectMaster->id
        );

        $this->response->assertStatus(404);
    }
}
