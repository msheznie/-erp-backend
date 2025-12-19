<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PriorityApiTest extends TestCase
{
    use MakePriorityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePriority()
    {
        $priority = $this->fakePriorityData();
        $this->json('POST', '/api/v1/priorities', $priority);

        $this->assertApiResponse($priority);
    }

    /**
     * @test
     */
    public function testReadPriority()
    {
        $priority = $this->makePriority();
        $this->json('GET', '/api/v1/priorities/'.$priority->id);

        $this->assertApiResponse($priority->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePriority()
    {
        $priority = $this->makePriority();
        $editedPriority = $this->fakePriorityData();

        $this->json('PUT', '/api/v1/priorities/'.$priority->id, $editedPriority);

        $this->assertApiResponse($editedPriority);
    }

    /**
     * @test
     */
    public function testDeletePriority()
    {
        $priority = $this->makePriority();
        $this->json('DELETE', '/api/v1/priorities/'.$priority->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/priorities/'.$priority->id);

        $this->assertResponseStatus(404);
    }
}
