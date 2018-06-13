<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterielRequestDetailsApiTest extends TestCase
{
    use MakeMaterielRequestDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMaterielRequestDetails()
    {
        $materielRequestDetails = $this->fakeMaterielRequestDetailsData();
        $this->json('POST', '/api/v1/materielRequestDetails', $materielRequestDetails);

        $this->assertApiResponse($materielRequestDetails);
    }

    /**
     * @test
     */
    public function testReadMaterielRequestDetails()
    {
        $materielRequestDetails = $this->makeMaterielRequestDetails();
        $this->json('GET', '/api/v1/materielRequestDetails/'.$materielRequestDetails->id);

        $this->assertApiResponse($materielRequestDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMaterielRequestDetails()
    {
        $materielRequestDetails = $this->makeMaterielRequestDetails();
        $editedMaterielRequestDetails = $this->fakeMaterielRequestDetailsData();

        $this->json('PUT', '/api/v1/materielRequestDetails/'.$materielRequestDetails->id, $editedMaterielRequestDetails);

        $this->assertApiResponse($editedMaterielRequestDetails);
    }

    /**
     * @test
     */
    public function testDeleteMaterielRequestDetails()
    {
        $materielRequestDetails = $this->makeMaterielRequestDetails();
        $this->json('DELETE', '/api/v1/materielRequestDetails/'.$materielRequestDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/materielRequestDetails/'.$materielRequestDetails->id);

        $this->assertResponseStatus(404);
    }
}
