<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DesignationApiTest extends TestCase
{
    use MakeDesignationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDesignation()
    {
        $designation = $this->fakeDesignationData();
        $this->json('POST', '/api/v1/designations', $designation);

        $this->assertApiResponse($designation);
    }

    /**
     * @test
     */
    public function testReadDesignation()
    {
        $designation = $this->makeDesignation();
        $this->json('GET', '/api/v1/designations/'.$designation->id);

        $this->assertApiResponse($designation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDesignation()
    {
        $designation = $this->makeDesignation();
        $editedDesignation = $this->fakeDesignationData();

        $this->json('PUT', '/api/v1/designations/'.$designation->id, $editedDesignation);

        $this->assertApiResponse($editedDesignation);
    }

    /**
     * @test
     */
    public function testDeleteDesignation()
    {
        $designation = $this->makeDesignation();
        $this->json('DELETE', '/api/v1/designations/'.$designation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/designations/'.$designation->id);

        $this->assertResponseStatus(404);
    }
}
