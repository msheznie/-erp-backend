<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnMasterApiTest extends TestCase
{
    use MakeItemReturnMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemReturnMaster()
    {
        $itemReturnMaster = $this->fakeItemReturnMasterData();
        $this->json('POST', '/api/v1/itemReturnMasters', $itemReturnMaster);

        $this->assertApiResponse($itemReturnMaster);
    }

    /**
     * @test
     */
    public function testReadItemReturnMaster()
    {
        $itemReturnMaster = $this->makeItemReturnMaster();
        $this->json('GET', '/api/v1/itemReturnMasters/'.$itemReturnMaster->id);

        $this->assertApiResponse($itemReturnMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemReturnMaster()
    {
        $itemReturnMaster = $this->makeItemReturnMaster();
        $editedItemReturnMaster = $this->fakeItemReturnMasterData();

        $this->json('PUT', '/api/v1/itemReturnMasters/'.$itemReturnMaster->id, $editedItemReturnMaster);

        $this->assertApiResponse($editedItemReturnMaster);
    }

    /**
     * @test
     */
    public function testDeleteItemReturnMaster()
    {
        $itemReturnMaster = $this->makeItemReturnMaster();
        $this->json('DELETE', '/api/v1/itemReturnMasters/'.$itemReturnMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemReturnMasters/'.$itemReturnMaster->id);

        $this->assertResponseStatus(404);
    }
}
