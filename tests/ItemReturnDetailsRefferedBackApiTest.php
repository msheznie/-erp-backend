<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnDetailsRefferedBackApiTest extends TestCase
{
    use MakeItemReturnDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->fakeItemReturnDetailsRefferedBackData();
        $this->json('POST', '/api/v1/itemReturnDetailsRefferedBacks', $itemReturnDetailsRefferedBack);

        $this->assertApiResponse($itemReturnDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->makeItemReturnDetailsRefferedBack();
        $this->json('GET', '/api/v1/itemReturnDetailsRefferedBacks/'.$itemReturnDetailsRefferedBack->id);

        $this->assertApiResponse($itemReturnDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->makeItemReturnDetailsRefferedBack();
        $editedItemReturnDetailsRefferedBack = $this->fakeItemReturnDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/itemReturnDetailsRefferedBacks/'.$itemReturnDetailsRefferedBack->id, $editedItemReturnDetailsRefferedBack);

        $this->assertApiResponse($editedItemReturnDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->makeItemReturnDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/itemReturnDetailsRefferedBacks/'.$itemReturnDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemReturnDetailsRefferedBacks/'.$itemReturnDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
