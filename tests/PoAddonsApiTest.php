<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoAddonsApiTest extends TestCase
{
    use MakePoAddonsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePoAddons()
    {
        $poAddons = $this->fakePoAddonsData();
        $this->json('POST', '/api/v1/poAddons', $poAddons);

        $this->assertApiResponse($poAddons);
    }

    /**
     * @test
     */
    public function testReadPoAddons()
    {
        $poAddons = $this->makePoAddons();
        $this->json('GET', '/api/v1/poAddons/'.$poAddons->id);

        $this->assertApiResponse($poAddons->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePoAddons()
    {
        $poAddons = $this->makePoAddons();
        $editedPoAddons = $this->fakePoAddonsData();

        $this->json('PUT', '/api/v1/poAddons/'.$poAddons->id, $editedPoAddons);

        $this->assertApiResponse($editedPoAddons);
    }

    /**
     * @test
     */
    public function testDeletePoAddons()
    {
        $poAddons = $this->makePoAddons();
        $this->json('DELETE', '/api/v1/poAddons/'.$poAddons->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/poAddons/'.$poAddons->id);

        $this->assertResponseStatus(404);
    }
}
