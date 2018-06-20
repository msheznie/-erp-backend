<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueMasterApiTest extends TestCase
{
    use MakeItemIssueMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemIssueMaster()
    {
        $itemIssueMaster = $this->fakeItemIssueMasterData();
        $this->json('POST', '/api/v1/itemIssueMasters', $itemIssueMaster);

        $this->assertApiResponse($itemIssueMaster);
    }

    /**
     * @test
     */
    public function testReadItemIssueMaster()
    {
        $itemIssueMaster = $this->makeItemIssueMaster();
        $this->json('GET', '/api/v1/itemIssueMasters/'.$itemIssueMaster->id);

        $this->assertApiResponse($itemIssueMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemIssueMaster()
    {
        $itemIssueMaster = $this->makeItemIssueMaster();
        $editedItemIssueMaster = $this->fakeItemIssueMasterData();

        $this->json('PUT', '/api/v1/itemIssueMasters/'.$itemIssueMaster->id, $editedItemIssueMaster);

        $this->assertApiResponse($editedItemIssueMaster);
    }

    /**
     * @test
     */
    public function testDeleteItemIssueMaster()
    {
        $itemIssueMaster = $this->makeItemIssueMaster();
        $this->json('DELETE', '/api/v1/itemIssueMasters/'.$itemIssueMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemIssueMasters/'.$itemIssueMaster->id);

        $this->assertResponseStatus(404);
    }
}
