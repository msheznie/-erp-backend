<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSJvMasterApiTest extends TestCase
{
    use MakeHRMSJvMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateHRMSJvMaster()
    {
        $hRMSJvMaster = $this->fakeHRMSJvMasterData();
        $this->json('POST', '/api/v1/hRMSJvMasters', $hRMSJvMaster);

        $this->assertApiResponse($hRMSJvMaster);
    }

    /**
     * @test
     */
    public function testReadHRMSJvMaster()
    {
        $hRMSJvMaster = $this->makeHRMSJvMaster();
        $this->json('GET', '/api/v1/hRMSJvMasters/'.$hRMSJvMaster->id);

        $this->assertApiResponse($hRMSJvMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateHRMSJvMaster()
    {
        $hRMSJvMaster = $this->makeHRMSJvMaster();
        $editedHRMSJvMaster = $this->fakeHRMSJvMasterData();

        $this->json('PUT', '/api/v1/hRMSJvMasters/'.$hRMSJvMaster->id, $editedHRMSJvMaster);

        $this->assertApiResponse($editedHRMSJvMaster);
    }

    /**
     * @test
     */
    public function testDeleteHRMSJvMaster()
    {
        $hRMSJvMaster = $this->makeHRMSJvMaster();
        $this->json('DELETE', '/api/v1/hRMSJvMasters/'.$hRMSJvMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/hRMSJvMasters/'.$hRMSJvMaster->id);

        $this->assertResponseStatus(404);
    }
}
