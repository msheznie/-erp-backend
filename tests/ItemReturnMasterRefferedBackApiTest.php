<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnMasterRefferedBackApiTest extends TestCase
{
    use MakeItemReturnMasterRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->fakeItemReturnMasterRefferedBackData();
        $this->json('POST', '/api/v1/itemReturnMasterRefferedBacks', $itemReturnMasterRefferedBack);

        $this->assertApiResponse($itemReturnMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testReadItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->makeItemReturnMasterRefferedBack();
        $this->json('GET', '/api/v1/itemReturnMasterRefferedBacks/'.$itemReturnMasterRefferedBack->id);

        $this->assertApiResponse($itemReturnMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->makeItemReturnMasterRefferedBack();
        $editedItemReturnMasterRefferedBack = $this->fakeItemReturnMasterRefferedBackData();

        $this->json('PUT', '/api/v1/itemReturnMasterRefferedBacks/'.$itemReturnMasterRefferedBack->id, $editedItemReturnMasterRefferedBack);

        $this->assertApiResponse($editedItemReturnMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->makeItemReturnMasterRefferedBack();
        $this->json('DELETE', '/api/v1/itemReturnMasterRefferedBacks/'.$itemReturnMasterRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemReturnMasterRefferedBacks/'.$itemReturnMasterRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
