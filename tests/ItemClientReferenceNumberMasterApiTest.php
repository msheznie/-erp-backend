<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemClientReferenceNumberMasterApiTest extends TestCase
{
    use MakeItemClientReferenceNumberMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->fakeItemClientReferenceNumberMasterData();
        $this->json('POST', '/api/v1/itemClientReferenceNumberMasters', $itemClientReferenceNumberMaster);

        $this->assertApiResponse($itemClientReferenceNumberMaster);
    }

    /**
     * @test
     */
    public function testReadItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->makeItemClientReferenceNumberMaster();
        $this->json('GET', '/api/v1/itemClientReferenceNumberMasters/'.$itemClientReferenceNumberMaster->id);

        $this->assertApiResponse($itemClientReferenceNumberMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->makeItemClientReferenceNumberMaster();
        $editedItemClientReferenceNumberMaster = $this->fakeItemClientReferenceNumberMasterData();

        $this->json('PUT', '/api/v1/itemClientReferenceNumberMasters/'.$itemClientReferenceNumberMaster->id, $editedItemClientReferenceNumberMaster);

        $this->assertApiResponse($editedItemClientReferenceNumberMaster);
    }

    /**
     * @test
     */
    public function testDeleteItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->makeItemClientReferenceNumberMaster();
        $this->json('DELETE', '/api/v1/itemClientReferenceNumberMasters/'.$itemClientReferenceNumberMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemClientReferenceNumberMasters/'.$itemClientReferenceNumberMaster->id);

        $this->assertResponseStatus(404);
    }
}
