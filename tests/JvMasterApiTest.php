<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvMasterApiTest extends TestCase
{
    use MakeJvMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateJvMaster()
    {
        $jvMaster = $this->fakeJvMasterData();
        $this->json('POST', '/api/v1/jvMasters', $jvMaster);

        $this->assertApiResponse($jvMaster);
    }

    /**
     * @test
     */
    public function testReadJvMaster()
    {
        $jvMaster = $this->makeJvMaster();
        $this->json('GET', '/api/v1/jvMasters/'.$jvMaster->id);

        $this->assertApiResponse($jvMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateJvMaster()
    {
        $jvMaster = $this->makeJvMaster();
        $editedJvMaster = $this->fakeJvMasterData();

        $this->json('PUT', '/api/v1/jvMasters/'.$jvMaster->id, $editedJvMaster);

        $this->assertApiResponse($editedJvMaster);
    }

    /**
     * @test
     */
    public function testDeleteJvMaster()
    {
        $jvMaster = $this->makeJvMaster();
        $this->json('DELETE', '/api/v1/jvMasters/'.$jvMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/jvMasters/'.$jvMaster->id);

        $this->assertResponseStatus(404);
    }
}
