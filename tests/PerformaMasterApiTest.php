<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerformaMasterApiTest extends TestCase
{
    use MakePerformaMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePerformaMaster()
    {
        $performaMaster = $this->fakePerformaMasterData();
        $this->json('POST', '/api/v1/performaMasters', $performaMaster);

        $this->assertApiResponse($performaMaster);
    }

    /**
     * @test
     */
    public function testReadPerformaMaster()
    {
        $performaMaster = $this->makePerformaMaster();
        $this->json('GET', '/api/v1/performaMasters/'.$performaMaster->id);

        $this->assertApiResponse($performaMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePerformaMaster()
    {
        $performaMaster = $this->makePerformaMaster();
        $editedPerformaMaster = $this->fakePerformaMasterData();

        $this->json('PUT', '/api/v1/performaMasters/'.$performaMaster->id, $editedPerformaMaster);

        $this->assertApiResponse($editedPerformaMaster);
    }

    /**
     * @test
     */
    public function testDeletePerformaMaster()
    {
        $performaMaster = $this->makePerformaMaster();
        $this->json('DELETE', '/api/v1/performaMasters/'.$performaMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/performaMasters/'.$performaMaster->id);

        $this->assertResponseStatus(404);
    }
}
