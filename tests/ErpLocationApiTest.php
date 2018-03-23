<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ErpLocationApiTest extends TestCase
{
    use MakeErpLocationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateErpLocation()
    {
        $erpLocation = $this->fakeErpLocationData();
        $this->json('POST', '/api/v1/erpLocations', $erpLocation);

        $this->assertApiResponse($erpLocation);
    }

    /**
     * @test
     */
    public function testReadErpLocation()
    {
        $erpLocation = $this->makeErpLocation();
        $this->json('GET', '/api/v1/erpLocations/'.$erpLocation->id);

        $this->assertApiResponse($erpLocation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateErpLocation()
    {
        $erpLocation = $this->makeErpLocation();
        $editedErpLocation = $this->fakeErpLocationData();

        $this->json('PUT', '/api/v1/erpLocations/'.$erpLocation->id, $editedErpLocation);

        $this->assertApiResponse($editedErpLocation);
    }

    /**
     * @test
     */
    public function testDeleteErpLocation()
    {
        $erpLocation = $this->makeErpLocation();
        $this->json('DELETE', '/api/v1/erpLocations/'.$erpLocation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/erpLocations/'.$erpLocation->id);

        $this->assertResponseStatus(404);
    }
}
