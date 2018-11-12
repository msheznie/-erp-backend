<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSDepartmentMasterApiTest extends TestCase
{
    use MakeHRMSDepartmentMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->fakeHRMSDepartmentMasterData();
        $this->json('POST', '/api/v1/hRMSDepartmentMasters', $hRMSDepartmentMaster);

        $this->assertApiResponse($hRMSDepartmentMaster);
    }

    /**
     * @test
     */
    public function testReadHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->makeHRMSDepartmentMaster();
        $this->json('GET', '/api/v1/hRMSDepartmentMasters/'.$hRMSDepartmentMaster->id);

        $this->assertApiResponse($hRMSDepartmentMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->makeHRMSDepartmentMaster();
        $editedHRMSDepartmentMaster = $this->fakeHRMSDepartmentMasterData();

        $this->json('PUT', '/api/v1/hRMSDepartmentMasters/'.$hRMSDepartmentMaster->id, $editedHRMSDepartmentMaster);

        $this->assertApiResponse($editedHRMSDepartmentMaster);
    }

    /**
     * @test
     */
    public function testDeleteHRMSDepartmentMaster()
    {
        $hRMSDepartmentMaster = $this->makeHRMSDepartmentMaster();
        $this->json('DELETE', '/api/v1/hRMSDepartmentMasters/'.$hRMSDepartmentMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/hRMSDepartmentMasters/'.$hRMSDepartmentMaster->id);

        $this->assertResponseStatus(404);
    }
}
