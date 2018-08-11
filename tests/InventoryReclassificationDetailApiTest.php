<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryReclassificationDetailApiTest extends TestCase
{
    use MakeInventoryReclassificationDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->fakeInventoryReclassificationDetailData();
        $this->json('POST', '/api/v1/inventoryReclassificationDetails', $inventoryReclassificationDetail);

        $this->assertApiResponse($inventoryReclassificationDetail);
    }

    /**
     * @test
     */
    public function testReadInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->makeInventoryReclassificationDetail();
        $this->json('GET', '/api/v1/inventoryReclassificationDetails/'.$inventoryReclassificationDetail->id);

        $this->assertApiResponse($inventoryReclassificationDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->makeInventoryReclassificationDetail();
        $editedInventoryReclassificationDetail = $this->fakeInventoryReclassificationDetailData();

        $this->json('PUT', '/api/v1/inventoryReclassificationDetails/'.$inventoryReclassificationDetail->id, $editedInventoryReclassificationDetail);

        $this->assertApiResponse($editedInventoryReclassificationDetail);
    }

    /**
     * @test
     */
    public function testDeleteInventoryReclassificationDetail()
    {
        $inventoryReclassificationDetail = $this->makeInventoryReclassificationDetail();
        $this->json('DELETE', '/api/v1/inventoryReclassificationDetails/'.$inventoryReclassificationDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/inventoryReclassificationDetails/'.$inventoryReclassificationDetail->id);

        $this->assertResponseStatus(404);
    }
}
