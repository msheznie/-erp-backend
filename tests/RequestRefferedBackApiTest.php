<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RequestRefferedBackApiTest extends TestCase
{
    use MakeRequestRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateRequestRefferedBack()
    {
        $requestRefferedBack = $this->fakeRequestRefferedBackData();
        $this->json('POST', '/api/v1/requestRefferedBacks', $requestRefferedBack);

        $this->assertApiResponse($requestRefferedBack);
    }

    /**
     * @test
     */
    public function testReadRequestRefferedBack()
    {
        $requestRefferedBack = $this->makeRequestRefferedBack();
        $this->json('GET', '/api/v1/requestRefferedBacks/'.$requestRefferedBack->id);

        $this->assertApiResponse($requestRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateRequestRefferedBack()
    {
        $requestRefferedBack = $this->makeRequestRefferedBack();
        $editedRequestRefferedBack = $this->fakeRequestRefferedBackData();

        $this->json('PUT', '/api/v1/requestRefferedBacks/'.$requestRefferedBack->id, $editedRequestRefferedBack);

        $this->assertApiResponse($editedRequestRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteRequestRefferedBack()
    {
        $requestRefferedBack = $this->makeRequestRefferedBack();
        $this->json('DELETE', '/api/v1/requestRefferedBacks/'.$requestRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/requestRefferedBacks/'.$requestRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
