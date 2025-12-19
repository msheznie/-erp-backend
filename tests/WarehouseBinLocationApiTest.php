<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseBinLocationApiTest extends TestCase
{
    use MakeWarehouseBinLocationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->fakeWarehouseBinLocationData();
        $this->json('POST', '/api/v1/warehouseBinLocations', $warehouseBinLocation);

        $this->assertApiResponse($warehouseBinLocation);
    }

    /**
     * @test
     */
    public function testReadWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->makeWarehouseBinLocation();
        $this->json('GET', '/api/v1/warehouseBinLocations/'.$warehouseBinLocation->id);

        $this->assertApiResponse($warehouseBinLocation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->makeWarehouseBinLocation();
        $editedWarehouseBinLocation = $this->fakeWarehouseBinLocationData();

        $this->json('PUT', '/api/v1/warehouseBinLocations/'.$warehouseBinLocation->id, $editedWarehouseBinLocation);

        $this->assertApiResponse($editedWarehouseBinLocation);
    }

    /**
     * @test
     */
    public function testDeleteWarehouseBinLocation()
    {
        $warehouseBinLocation = $this->makeWarehouseBinLocation();
        $this->json('DELETE', '/api/v1/warehouseBinLocations/'.$warehouseBinLocation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/warehouseBinLocations/'.$warehouseBinLocation->id);

        $this->assertResponseStatus(404);
    }
}
