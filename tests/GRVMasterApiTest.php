<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GRVMasterApiTest extends TestCase
{
    use MakeGRVMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGRVMaster()
    {
        $gRVMaster = $this->fakeGRVMasterData();
        $this->json('POST', '/api/v1/gRVMasters', $gRVMaster);

        $this->assertApiResponse($gRVMaster);
    }

    /**
     * @test
     */
    public function testReadGRVMaster()
    {
        $gRVMaster = $this->makeGRVMaster();
        $this->json('GET', '/api/v1/gRVMasters/'.$gRVMaster->id);

        $this->assertApiResponse($gRVMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGRVMaster()
    {
        $gRVMaster = $this->makeGRVMaster();
        $editedGRVMaster = $this->fakeGRVMasterData();

        $this->json('PUT', '/api/v1/gRVMasters/'.$gRVMaster->id, $editedGRVMaster);

        $this->assertApiResponse($editedGRVMaster);
    }

    /**
     * @test
     */
    public function testDeleteGRVMaster()
    {
        $gRVMaster = $this->makeGRVMaster();
        $this->json('DELETE', '/api/v1/gRVMasters/'.$gRVMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gRVMasters/'.$gRVMaster->id);

        $this->assertResponseStatus(404);
    }
}
