<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoAddonsRefferedBackApiTest extends TestCase
{
    use MakePoAddonsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->fakePoAddonsRefferedBackData();
        $this->json('POST', '/api/v1/poAddonsRefferedBacks', $poAddonsRefferedBack);

        $this->assertApiResponse($poAddonsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadPoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->makePoAddonsRefferedBack();
        $this->json('GET', '/api/v1/poAddonsRefferedBacks/'.$poAddonsRefferedBack->id);

        $this->assertApiResponse($poAddonsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->makePoAddonsRefferedBack();
        $editedPoAddonsRefferedBack = $this->fakePoAddonsRefferedBackData();

        $this->json('PUT', '/api/v1/poAddonsRefferedBacks/'.$poAddonsRefferedBack->id, $editedPoAddonsRefferedBack);

        $this->assertApiResponse($editedPoAddonsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeletePoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->makePoAddonsRefferedBack();
        $this->json('DELETE', '/api/v1/poAddonsRefferedBacks/'.$poAddonsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/poAddonsRefferedBacks/'.$poAddonsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
