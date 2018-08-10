<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryReclassificationApiTest extends TestCase
{
    use MakeInventoryReclassificationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateInventoryReclassification()
    {
        $inventoryReclassification = $this->fakeInventoryReclassificationData();
        $this->json('POST', '/api/v1/inventoryReclassifications', $inventoryReclassification);

        $this->assertApiResponse($inventoryReclassification);
    }

    /**
     * @test
     */
    public function testReadInventoryReclassification()
    {
        $inventoryReclassification = $this->makeInventoryReclassification();
        $this->json('GET', '/api/v1/inventoryReclassifications/'.$inventoryReclassification->id);

        $this->assertApiResponse($inventoryReclassification->toArray());
    }

    /**
     * @test
     */
    public function testUpdateInventoryReclassification()
    {
        $inventoryReclassification = $this->makeInventoryReclassification();
        $editedInventoryReclassification = $this->fakeInventoryReclassificationData();

        $this->json('PUT', '/api/v1/inventoryReclassifications/'.$inventoryReclassification->id, $editedInventoryReclassification);

        $this->assertApiResponse($editedInventoryReclassification);
    }

    /**
     * @test
     */
    public function testDeleteInventoryReclassification()
    {
        $inventoryReclassification = $this->makeInventoryReclassification();
        $this->json('DELETE', '/api/v1/inventoryReclassifications/'.$inventoryReclassification->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/inventoryReclassifications/'.$inventoryReclassification->id);

        $this->assertResponseStatus(404);
    }
}
