<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueMasterRefferedBackApiTest extends TestCase
{
    use MakeItemIssueMasterRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->fakeItemIssueMasterRefferedBackData();
        $this->json('POST', '/api/v1/itemIssueMasterRefferedBacks', $itemIssueMasterRefferedBack);

        $this->assertApiResponse($itemIssueMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testReadItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->makeItemIssueMasterRefferedBack();
        $this->json('GET', '/api/v1/itemIssueMasterRefferedBacks/'.$itemIssueMasterRefferedBack->id);

        $this->assertApiResponse($itemIssueMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->makeItemIssueMasterRefferedBack();
        $editedItemIssueMasterRefferedBack = $this->fakeItemIssueMasterRefferedBackData();

        $this->json('PUT', '/api/v1/itemIssueMasterRefferedBacks/'.$itemIssueMasterRefferedBack->id, $editedItemIssueMasterRefferedBack);

        $this->assertApiResponse($editedItemIssueMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->makeItemIssueMasterRefferedBack();
        $this->json('DELETE', '/api/v1/itemIssueMasterRefferedBacks/'.$itemIssueMasterRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemIssueMasterRefferedBacks/'.$itemIssueMasterRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
