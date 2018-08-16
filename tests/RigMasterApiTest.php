<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RigMasterApiTest extends TestCase
{
    use MakeRigMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateRigMaster()
    {
        $rigMaster = $this->fakeRigMasterData();
        $this->json('POST', '/api/v1/rigMasters', $rigMaster);

        $this->assertApiResponse($rigMaster);
    }

    /**
     * @test
     */
    public function testReadRigMaster()
    {
        $rigMaster = $this->makeRigMaster();
        $this->json('GET', '/api/v1/rigMasters/'.$rigMaster->id);

        $this->assertApiResponse($rigMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateRigMaster()
    {
        $rigMaster = $this->makeRigMaster();
        $editedRigMaster = $this->fakeRigMasterData();

        $this->json('PUT', '/api/v1/rigMasters/'.$rigMaster->id, $editedRigMaster);

        $this->assertApiResponse($editedRigMaster);
    }

    /**
     * @test
     */
    public function testDeleteRigMaster()
    {
        $rigMaster = $this->makeRigMaster();
        $this->json('DELETE', '/api/v1/rigMasters/'.$rigMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/rigMasters/'.$rigMaster->id);

        $this->assertResponseStatus(404);
    }
}
