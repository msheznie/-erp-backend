<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookInvSuppDetRefferedBackApiTest extends TestCase
{
    use MakeBookInvSuppDetRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->fakeBookInvSuppDetRefferedBackData();
        $this->json('POST', '/api/v1/bookInvSuppDetRefferedBacks', $bookInvSuppDetRefferedBack);

        $this->assertApiResponse($bookInvSuppDetRefferedBack);
    }

    /**
     * @test
     */
    public function testReadBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->makeBookInvSuppDetRefferedBack();
        $this->json('GET', '/api/v1/bookInvSuppDetRefferedBacks/'.$bookInvSuppDetRefferedBack->id);

        $this->assertApiResponse($bookInvSuppDetRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->makeBookInvSuppDetRefferedBack();
        $editedBookInvSuppDetRefferedBack = $this->fakeBookInvSuppDetRefferedBackData();

        $this->json('PUT', '/api/v1/bookInvSuppDetRefferedBacks/'.$bookInvSuppDetRefferedBack->id, $editedBookInvSuppDetRefferedBack);

        $this->assertApiResponse($editedBookInvSuppDetRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteBookInvSuppDetRefferedBack()
    {
        $bookInvSuppDetRefferedBack = $this->makeBookInvSuppDetRefferedBack();
        $this->json('DELETE', '/api/v1/bookInvSuppDetRefferedBacks/'.$bookInvSuppDetRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/bookInvSuppDetRefferedBacks/'.$bookInvSuppDetRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
