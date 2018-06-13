<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterielRequestApiTest extends TestCase
{
    use MakeMaterielRequestTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMaterielRequest()
    {
        $materielRequest = $this->fakeMaterielRequestData();
        $this->json('POST', '/api/v1/materielRequests', $materielRequest);

        $this->assertApiResponse($materielRequest);
    }

    /**
     * @test
     */
    public function testReadMaterielRequest()
    {
        $materielRequest = $this->makeMaterielRequest();
        $this->json('GET', '/api/v1/materielRequests/'.$materielRequest->id);

        $this->assertApiResponse($materielRequest->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMaterielRequest()
    {
        $materielRequest = $this->makeMaterielRequest();
        $editedMaterielRequest = $this->fakeMaterielRequestData();

        $this->json('PUT', '/api/v1/materielRequests/'.$materielRequest->id, $editedMaterielRequest);

        $this->assertApiResponse($editedMaterielRequest);
    }

    /**
     * @test
     */
    public function testDeleteMaterielRequest()
    {
        $materielRequest = $this->makeMaterielRequest();
        $this->json('DELETE', '/api/v1/materielRequests/'.$materielRequest->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/materielRequests/'.$materielRequest->id);

        $this->assertResponseStatus(404);
    }
}
