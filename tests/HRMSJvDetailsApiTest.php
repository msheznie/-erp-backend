<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSJvDetailsApiTest extends TestCase
{
    use MakeHRMSJvDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateHRMSJvDetails()
    {
        $hRMSJvDetails = $this->fakeHRMSJvDetailsData();
        $this->json('POST', '/api/v1/hRMSJvDetails', $hRMSJvDetails);

        $this->assertApiResponse($hRMSJvDetails);
    }

    /**
     * @test
     */
    public function testReadHRMSJvDetails()
    {
        $hRMSJvDetails = $this->makeHRMSJvDetails();
        $this->json('GET', '/api/v1/hRMSJvDetails/'.$hRMSJvDetails->id);

        $this->assertApiResponse($hRMSJvDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateHRMSJvDetails()
    {
        $hRMSJvDetails = $this->makeHRMSJvDetails();
        $editedHRMSJvDetails = $this->fakeHRMSJvDetailsData();

        $this->json('PUT', '/api/v1/hRMSJvDetails/'.$hRMSJvDetails->id, $editedHRMSJvDetails);

        $this->assertApiResponse($editedHRMSJvDetails);
    }

    /**
     * @test
     */
    public function testDeleteHRMSJvDetails()
    {
        $hRMSJvDetails = $this->makeHRMSJvDetails();
        $this->json('DELETE', '/api/v1/hRMSJvDetails/'.$hRMSJvDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/hRMSJvDetails/'.$hRMSJvDetails->id);

        $this->assertResponseStatus(404);
    }
}
