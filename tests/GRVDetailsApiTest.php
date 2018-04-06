<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GRVDetailsApiTest extends TestCase
{
    use MakeGRVDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGRVDetails()
    {
        $gRVDetails = $this->fakeGRVDetailsData();
        $this->json('POST', '/api/v1/gRVDetails', $gRVDetails);

        $this->assertApiResponse($gRVDetails);
    }

    /**
     * @test
     */
    public function testReadGRVDetails()
    {
        $gRVDetails = $this->makeGRVDetails();
        $this->json('GET', '/api/v1/gRVDetails/'.$gRVDetails->id);

        $this->assertApiResponse($gRVDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGRVDetails()
    {
        $gRVDetails = $this->makeGRVDetails();
        $editedGRVDetails = $this->fakeGRVDetailsData();

        $this->json('PUT', '/api/v1/gRVDetails/'.$gRVDetails->id, $editedGRVDetails);

        $this->assertApiResponse($editedGRVDetails);
    }

    /**
     * @test
     */
    public function testDeleteGRVDetails()
    {
        $gRVDetails = $this->makeGRVDetails();
        $this->json('DELETE', '/api/v1/gRVDetails/'.$gRVDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gRVDetails/'.$gRVDetails->id);

        $this->assertResponseStatus(404);
    }
}
