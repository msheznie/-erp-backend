<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class suppliernatureApiTest extends TestCase
{
    use MakesuppliernatureTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatesuppliernature()
    {
        $suppliernature = $this->fakesuppliernatureData();
        $this->json('POST', '/api/v1/suppliernatures', $suppliernature);

        $this->assertApiResponse($suppliernature);
    }

    /**
     * @test
     */
    public function testReadsuppliernature()
    {
        $suppliernature = $this->makesuppliernature();
        $this->json('GET', '/api/v1/suppliernatures/'.$suppliernature->id);

        $this->assertApiResponse($suppliernature->toArray());
    }

    /**
     * @test
     */
    public function testUpdatesuppliernature()
    {
        $suppliernature = $this->makesuppliernature();
        $editedsuppliernature = $this->fakesuppliernatureData();

        $this->json('PUT', '/api/v1/suppliernatures/'.$suppliernature->id, $editedsuppliernature);

        $this->assertApiResponse($editedsuppliernature);
    }

    /**
     * @test
     */
    public function testDeletesuppliernature()
    {
        $suppliernature = $this->makesuppliernature();
        $this->json('DELETE', '/api/v1/suppliernatures/'.$suppliernature->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/suppliernatures/'.$suppliernature->id);

        $this->assertResponseStatus(404);
    }
}
