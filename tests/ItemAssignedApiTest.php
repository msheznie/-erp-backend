<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemAssignedApiTest extends TestCase
{
    use MakeItemAssignedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemAssigned()
    {
        $itemAssigned = $this->fakeItemAssignedData();
        $this->json('POST', '/api/v1/itemAssigneds', $itemAssigned);

        $this->assertApiResponse($itemAssigned);
    }

    /**
     * @test
     */
    public function testReadItemAssigned()
    {
        $itemAssigned = $this->makeItemAssigned();
        $this->json('GET', '/api/v1/itemAssigneds/'.$itemAssigned->id);

        $this->assertApiResponse($itemAssigned->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemAssigned()
    {
        $itemAssigned = $this->makeItemAssigned();
        $editedItemAssigned = $this->fakeItemAssignedData();

        $this->json('PUT', '/api/v1/itemAssigneds/'.$itemAssigned->id, $editedItemAssigned);

        $this->assertApiResponse($editedItemAssigned);
    }

    /**
     * @test
     */
    public function testDeleteItemAssigned()
    {
        $itemAssigned = $this->makeItemAssigned();
        $this->json('DELETE', '/api/v1/itemAssigneds/'.$itemAssigned->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemAssigneds/'.$itemAssigned->id);

        $this->assertResponseStatus(404);
    }
}
