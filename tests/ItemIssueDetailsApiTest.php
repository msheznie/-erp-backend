<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueDetailsApiTest extends TestCase
{
    use MakeItemIssueDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemIssueDetails()
    {
        $itemIssueDetails = $this->fakeItemIssueDetailsData();
        $this->json('POST', '/api/v1/itemIssueDetails', $itemIssueDetails);

        $this->assertApiResponse($itemIssueDetails);
    }

    /**
     * @test
     */
    public function testReadItemIssueDetails()
    {
        $itemIssueDetails = $this->makeItemIssueDetails();
        $this->json('GET', '/api/v1/itemIssueDetails/'.$itemIssueDetails->id);

        $this->assertApiResponse($itemIssueDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemIssueDetails()
    {
        $itemIssueDetails = $this->makeItemIssueDetails();
        $editedItemIssueDetails = $this->fakeItemIssueDetailsData();

        $this->json('PUT', '/api/v1/itemIssueDetails/'.$itemIssueDetails->id, $editedItemIssueDetails);

        $this->assertApiResponse($editedItemIssueDetails);
    }

    /**
     * @test
     */
    public function testDeleteItemIssueDetails()
    {
        $itemIssueDetails = $this->makeItemIssueDetails();
        $this->json('DELETE', '/api/v1/itemIssueDetails/'.$itemIssueDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemIssueDetails/'.$itemIssueDetails->id);

        $this->assertResponseStatus(404);
    }
}
