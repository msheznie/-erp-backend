<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppMasterApiTest extends TestCase
{
    use MakeBookInvSuppMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->fakeBookInvSuppMasterData();
        $this->json('POST', '/api/v1/bookInvSuppMasters', $bookInvSuppMaster);

        $this->assertApiResponse($bookInvSuppMaster);
    }

    /**
     * @test
     */
    public function testReadBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->makeBookInvSuppMaster();
        $this->json('GET', '/api/v1/bookInvSuppMasters/'.$bookInvSuppMaster->id);

        $this->assertApiResponse($bookInvSuppMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->makeBookInvSuppMaster();
        $editedBookInvSuppMaster = $this->fakeBookInvSuppMasterData();

        $this->json('PUT', '/api/v1/bookInvSuppMasters/'.$bookInvSuppMaster->id, $editedBookInvSuppMaster);

        $this->assertApiResponse($editedBookInvSuppMaster);
    }

    /**
     * @test
     */
    public function testDeleteBookInvSuppMaster()
    {
        $bookInvSuppMaster = $this->makeBookInvSuppMaster();
        $this->json('DELETE', '/api/v1/bookInvSuppMasters/'.$bookInvSuppMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bookInvSuppMasters/'.$bookInvSuppMaster->id);

        $this->assertResponseStatus(404);
    }
}
