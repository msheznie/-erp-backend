<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueTypeApiTest extends TestCase
{
    use MakeItemIssueTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemIssueType()
    {
        $itemIssueType = $this->fakeItemIssueTypeData();
        $this->json('POST', '/api/v1/itemIssueTypes', $itemIssueType);

        $this->assertApiResponse($itemIssueType);
    }

    /**
     * @test
     */
    public function testReadItemIssueType()
    {
        $itemIssueType = $this->makeItemIssueType();
        $this->json('GET', '/api/v1/itemIssueTypes/'.$itemIssueType->id);

        $this->assertApiResponse($itemIssueType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemIssueType()
    {
        $itemIssueType = $this->makeItemIssueType();
        $editedItemIssueType = $this->fakeItemIssueTypeData();

        $this->json('PUT', '/api/v1/itemIssueTypes/'.$itemIssueType->id, $editedItemIssueType);

        $this->assertApiResponse($editedItemIssueType);
    }

    /**
     * @test
     */
    public function testDeleteItemIssueType()
    {
        $itemIssueType = $this->makeItemIssueType();
        $this->json('DELETE', '/api/v1/itemIssueTypes/'.$itemIssueType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemIssueTypes/'.$itemIssueType->id);

        $this->assertResponseStatus(404);
    }
}
