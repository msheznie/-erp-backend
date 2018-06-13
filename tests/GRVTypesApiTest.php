<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GRVTypesApiTest extends TestCase
{
    use MakeGRVTypesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGRVTypes()
    {
        $gRVTypes = $this->fakeGRVTypesData();
        $this->json('POST', '/api/v1/gRVTypes', $gRVTypes);

        $this->assertApiResponse($gRVTypes);
    }

    /**
     * @test
     */
    public function testReadGRVTypes()
    {
        $gRVTypes = $this->makeGRVTypes();
        $this->json('GET', '/api/v1/gRVTypes/'.$gRVTypes->id);

        $this->assertApiResponse($gRVTypes->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGRVTypes()
    {
        $gRVTypes = $this->makeGRVTypes();
        $editedGRVTypes = $this->fakeGRVTypesData();

        $this->json('PUT', '/api/v1/gRVTypes/'.$gRVTypes->id, $editedGRVTypes);

        $this->assertApiResponse($editedGRVTypes);
    }

    /**
     * @test
     */
    public function testDeleteGRVTypes()
    {
        $gRVTypes = $this->makeGRVTypes();
        $this->json('DELETE', '/api/v1/gRVTypes/'.$gRVTypes->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gRVTypes/'.$gRVTypes->id);

        $this->assertResponseStatus(404);
    }
}
