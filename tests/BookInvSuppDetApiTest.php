<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppDetApiTest extends TestCase
{
    use MakeBookInvSuppDetTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBookInvSuppDet()
    {
        $bookInvSuppDet = $this->fakeBookInvSuppDetData();
        $this->json('POST', '/api/v1/bookInvSuppDets', $bookInvSuppDet);

        $this->assertApiResponse($bookInvSuppDet);
    }

    /**
     * @test
     */
    public function testReadBookInvSuppDet()
    {
        $bookInvSuppDet = $this->makeBookInvSuppDet();
        $this->json('GET', '/api/v1/bookInvSuppDets/'.$bookInvSuppDet->id);

        $this->assertApiResponse($bookInvSuppDet->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBookInvSuppDet()
    {
        $bookInvSuppDet = $this->makeBookInvSuppDet();
        $editedBookInvSuppDet = $this->fakeBookInvSuppDetData();

        $this->json('PUT', '/api/v1/bookInvSuppDets/'.$bookInvSuppDet->id, $editedBookInvSuppDet);

        $this->assertApiResponse($editedBookInvSuppDet);
    }

    /**
     * @test
     */
    public function testDeleteBookInvSuppDet()
    {
        $bookInvSuppDet = $this->makeBookInvSuppDet();
        $this->json('DELETE', '/api/v1/bookInvSuppDets/'.$bookInvSuppDet->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bookInvSuppDets/'.$bookInvSuppDet->id);

        $this->assertResponseStatus(404);
    }
}
