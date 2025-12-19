<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WarehouseItemsApiTest extends TestCase
{
    use MakeWarehouseItemsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateWarehouseItems()
    {
        $warehouseItems = $this->fakeWarehouseItemsData();
        $this->json('POST', '/api/v1/warehouseItems', $warehouseItems);

        $this->assertApiResponse($warehouseItems);
    }

    /**
     * @test
     */
    public function testReadWarehouseItems()
    {
        $warehouseItems = $this->makeWarehouseItems();
        $this->json('GET', '/api/v1/warehouseItems/'.$warehouseItems->id);

        $this->assertApiResponse($warehouseItems->toArray());
    }

    /**
     * @test
     */
    public function testUpdateWarehouseItems()
    {
        $warehouseItems = $this->makeWarehouseItems();
        $editedWarehouseItems = $this->fakeWarehouseItemsData();

        $this->json('PUT', '/api/v1/warehouseItems/'.$warehouseItems->id, $editedWarehouseItems);

        $this->assertApiResponse($editedWarehouseItems);
    }

    /**
     * @test
     */
    public function testDeleteWarehouseItems()
    {
        $warehouseItems = $this->makeWarehouseItems();
        $this->json('DELETE', '/api/v1/warehouseItems/'.$warehouseItems->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/warehouseItems/'.$warehouseItems->id);

        $this->assertResponseStatus(404);
    }
}
