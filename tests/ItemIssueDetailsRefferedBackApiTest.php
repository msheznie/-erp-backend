<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueDetailsRefferedBackApiTest extends TestCase
{
    use MakeItemIssueDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->fakeItemIssueDetailsRefferedBackData();
        $this->json('POST', '/api/v1/itemIssueDetailsRefferedBacks', $itemIssueDetailsRefferedBack);

        $this->assertApiResponse($itemIssueDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->makeItemIssueDetailsRefferedBack();
        $this->json('GET', '/api/v1/itemIssueDetailsRefferedBacks/'.$itemIssueDetailsRefferedBack->id);

        $this->assertApiResponse($itemIssueDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->makeItemIssueDetailsRefferedBack();
        $editedItemIssueDetailsRefferedBack = $this->fakeItemIssueDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/itemIssueDetailsRefferedBacks/'.$itemIssueDetailsRefferedBack->id, $editedItemIssueDetailsRefferedBack);

        $this->assertApiResponse($editedItemIssueDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->makeItemIssueDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/itemIssueDetailsRefferedBacks/'.$itemIssueDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/itemIssueDetailsRefferedBacks/'.$itemIssueDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
