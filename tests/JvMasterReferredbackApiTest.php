<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvMasterReferredbackApiTest extends TestCase
{
    use MakeJvMasterReferredbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateJvMasterReferredback()
    {
        $jvMasterReferredback = $this->fakeJvMasterReferredbackData();
        $this->json('POST', '/api/v1/jvMasterReferredbacks', $jvMasterReferredback);

        $this->assertApiResponse($jvMasterReferredback);
    }

    /**
     * @test
     */
    public function testReadJvMasterReferredback()
    {
        $jvMasterReferredback = $this->makeJvMasterReferredback();
        $this->json('GET', '/api/v1/jvMasterReferredbacks/'.$jvMasterReferredback->id);

        $this->assertApiResponse($jvMasterReferredback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateJvMasterReferredback()
    {
        $jvMasterReferredback = $this->makeJvMasterReferredback();
        $editedJvMasterReferredback = $this->fakeJvMasterReferredbackData();

        $this->json('PUT', '/api/v1/jvMasterReferredbacks/'.$jvMasterReferredback->id, $editedJvMasterReferredback);

        $this->assertApiResponse($editedJvMasterReferredback);
    }

    /**
     * @test
     */
    public function testDeleteJvMasterReferredback()
    {
        $jvMasterReferredback = $this->makeJvMasterReferredback();
        $this->json('DELETE', '/api/v1/jvMasterReferredbacks/'.$jvMasterReferredback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/jvMasterReferredbacks/'.$jvMasterReferredback->id);

        $this->assertResponseStatus(404);
    }
}
