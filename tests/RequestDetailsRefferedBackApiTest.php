<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RequestDetailsRefferedBackApiTest extends TestCase
{
    use MakeRequestDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->fakeRequestDetailsRefferedBackData();
        $this->json('POST', '/api/v1/requestDetailsRefferedBacks', $requestDetailsRefferedBack);

        $this->assertApiResponse($requestDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->makeRequestDetailsRefferedBack();
        $this->json('GET', '/api/v1/requestDetailsRefferedBacks/'.$requestDetailsRefferedBack->id);

        $this->assertApiResponse($requestDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->makeRequestDetailsRefferedBack();
        $editedRequestDetailsRefferedBack = $this->fakeRequestDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/requestDetailsRefferedBacks/'.$requestDetailsRefferedBack->id, $editedRequestDetailsRefferedBack);

        $this->assertApiResponse($editedRequestDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->makeRequestDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/requestDetailsRefferedBacks/'.$requestDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/requestDetailsRefferedBacks/'.$requestDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
