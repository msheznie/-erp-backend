<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvDetailsReferredbackApiTest extends TestCase
{
    use MakeJvDetailsReferredbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->fakeJvDetailsReferredbackData();
        $this->json('POST', '/api/v1/jvDetailsReferredbacks', $jvDetailsReferredback);

        $this->assertApiResponse($jvDetailsReferredback);
    }

    /**
     * @test
     */
    public function testReadJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->makeJvDetailsReferredback();
        $this->json('GET', '/api/v1/jvDetailsReferredbacks/'.$jvDetailsReferredback->id);

        $this->assertApiResponse($jvDetailsReferredback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->makeJvDetailsReferredback();
        $editedJvDetailsReferredback = $this->fakeJvDetailsReferredbackData();

        $this->json('PUT', '/api/v1/jvDetailsReferredbacks/'.$jvDetailsReferredback->id, $editedJvDetailsReferredback);

        $this->assertApiResponse($editedJvDetailsReferredback);
    }

    /**
     * @test
     */
    public function testDeleteJvDetailsReferredback()
    {
        $jvDetailsReferredback = $this->makeJvDetailsReferredback();
        $this->json('DELETE', '/api/v1/jvDetailsReferredbacks/'.$jvDetailsReferredback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/jvDetailsReferredbacks/'.$jvDetailsReferredback->id);

        $this->assertResponseStatus(404);
    }
}
