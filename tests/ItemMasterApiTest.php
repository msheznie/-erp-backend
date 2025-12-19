<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemMasterApiTest extends TestCase
{
    use MakeItemMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemMaster()
    {
        $itemMaster = $this->fakeItemMasterData();
        $this->json('POST', '/api/v1/itemMasters', $itemMaster);

        $this->assertApiResponse($itemMaster);
    }

    /**
     * @test
     */
    public function testReadItemMaster()
    {
        $itemMaster = $this->makeItemMaster();
        $this->json('GET', '/api/v1/itemMasters/'.$itemMaster->id);

        $this->assertApiResponse($itemMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemMaster()
    {
        $itemMaster = $this->makeItemMaster();
        $editedItemMaster = $this->fakeItemMasterData();

        $this->json('PUT', '/api/v1/itemMasters/'.$itemMaster->id, $editedItemMaster);

        $this->assertApiResponse($editedItemMaster);
    }

    /**
     * @test
     */
    public function testDeleteItemMaster()
    {
        $itemMaster = $this->makeItemMaster();
        $this->json('DELETE', '/api/v1/itemMasters/'.$itemMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemMasters/'.$itemMaster->id);

        $this->assertResponseStatus(404);
    }
}
