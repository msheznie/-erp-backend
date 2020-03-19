<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHrmsDepartmentMasterTrait;
use Tests\ApiTestTrait;

class HrmsDepartmentMasterApiTest extends TestCase
{
    use MakeHrmsDepartmentMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->fakeHrmsDepartmentMasterData();
        $this->response = $this->json('POST', '/api/hrmsDepartmentMasters', $hrmsDepartmentMaster);

        $this->assertApiResponse($hrmsDepartmentMaster);
    }

    /**
     * @test
     */
    public function test_read_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->makeHrmsDepartmentMaster();
        $this->response = $this->json('GET', '/api/hrmsDepartmentMasters/'.$hrmsDepartmentMaster->id);

        $this->assertApiResponse($hrmsDepartmentMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->makeHrmsDepartmentMaster();
        $editedHrmsDepartmentMaster = $this->fakeHrmsDepartmentMasterData();

        $this->response = $this->json('PUT', '/api/hrmsDepartmentMasters/'.$hrmsDepartmentMaster->id, $editedHrmsDepartmentMaster);

        $this->assertApiResponse($editedHrmsDepartmentMaster);
    }

    /**
     * @test
     */
    public function test_delete_hrms_department_master()
    {
        $hrmsDepartmentMaster = $this->makeHrmsDepartmentMaster();
        $this->response = $this->json('DELETE', '/api/hrmsDepartmentMasters/'.$hrmsDepartmentMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hrmsDepartmentMasters/'.$hrmsDepartmentMaster->id);

        $this->response->assertStatus(404);
    }
}
