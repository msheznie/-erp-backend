<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppMasterRefferedBackApiTest extends TestCase
{
    use MakeBookInvSuppMasterRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->fakeBookInvSuppMasterRefferedBackData();
        $this->json('POST', '/api/v1/bookInvSuppMasterRefferedBacks', $bookInvSuppMasterRefferedBack);

        $this->assertApiResponse($bookInvSuppMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testReadBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->makeBookInvSuppMasterRefferedBack();
        $this->json('GET', '/api/v1/bookInvSuppMasterRefferedBacks/'.$bookInvSuppMasterRefferedBack->id);

        $this->assertApiResponse($bookInvSuppMasterRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->makeBookInvSuppMasterRefferedBack();
        $editedBookInvSuppMasterRefferedBack = $this->fakeBookInvSuppMasterRefferedBackData();

        $this->json('PUT', '/api/v1/bookInvSuppMasterRefferedBacks/'.$bookInvSuppMasterRefferedBack->id, $editedBookInvSuppMasterRefferedBack);

        $this->assertApiResponse($editedBookInvSuppMasterRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteBookInvSuppMasterRefferedBack()
    {
        $bookInvSuppMasterRefferedBack = $this->makeBookInvSuppMasterRefferedBack();
        $this->json('DELETE', '/api/v1/bookInvSuppMasterRefferedBacks/'.$bookInvSuppMasterRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bookInvSuppMasterRefferedBacks/'.$bookInvSuppMasterRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
