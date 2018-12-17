<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemMasterRefferedBackApiTest extends TestCase
{
    use MakeItemMasterRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->fakeItemMasterRefferedBackData();
        $this->json('POST', '/api/v1/itemMasterRefferedBacks', $itemMasterRefferedBack);

        $this->assertApiResponse($itemMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testReadItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->makeItemMasterRefferedBack();
        $this->json('GET', '/api/v1/itemMasterRefferedBacks/'.$itemMasterRefferedBack->id);

        $this->assertApiResponse($itemMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->makeItemMasterRefferedBack();
        $editedItemMasterRefferedBack = $this->fakeItemMasterRefferedBackData();

        $this->json('PUT', '/api/v1/itemMasterRefferedBacks/'.$itemMasterRefferedBack->id, $editedItemMasterRefferedBack);

        $this->assertApiResponse($editedItemMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->makeItemMasterRefferedBack();
        $this->json('DELETE', '/api/v1/itemMasterRefferedBacks/'.$itemMasterRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemMasterRefferedBacks/'.$itemMasterRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
