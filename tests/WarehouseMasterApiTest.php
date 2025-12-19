<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseMasterApiTest extends TestCase
{
    use MakeWarehouseMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWarehouseMaster()
    {
        $warehouseMaster = $this->fakeWarehouseMasterData();
        $this->json('POST', '/api/v1/warehouseMasters', $warehouseMaster);

        $this->assertApiResponse($warehouseMaster);
    }

    /**
     * @test
     */
    public function testReadWarehouseMaster()
    {
        $warehouseMaster = $this->makeWarehouseMaster();
        $this->json('GET', '/api/v1/warehouseMasters/'.$warehouseMaster->id);

        $this->assertApiResponse($warehouseMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWarehouseMaster()
    {
        $warehouseMaster = $this->makeWarehouseMaster();
        $editedWarehouseMaster = $this->fakeWarehouseMasterData();

        $this->json('PUT', '/api/v1/warehouseMasters/'.$warehouseMaster->id, $editedWarehouseMaster);

        $this->assertApiResponse($editedWarehouseMaster);
    }

    /**
     * @test
     */
    public function testDeleteWarehouseMaster()
    {
        $warehouseMaster = $this->makeWarehouseMaster();
        $this->json('DELETE', '/api/v1/warehouseMasters/'.$warehouseMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/warehouseMasters/'.$warehouseMaster->id);

        $this->assertResponseStatus(404);
    }
}
