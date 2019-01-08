<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposPaymentGlConfigMasterApiTest extends TestCase
{
    use MakeGposPaymentGlConfigMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->fakeGposPaymentGlConfigMasterData();
        $this->json('POST', '/api/v1/gposPaymentGlConfigMasters', $gposPaymentGlConfigMaster);

        $this->assertApiResponse($gposPaymentGlConfigMaster);
    }

    /**
     * @test
     */
    public function testReadGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->makeGposPaymentGlConfigMaster();
        $this->json('GET', '/api/v1/gposPaymentGlConfigMasters/'.$gposPaymentGlConfigMaster->id);

        $this->assertApiResponse($gposPaymentGlConfigMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->makeGposPaymentGlConfigMaster();
        $editedGposPaymentGlConfigMaster = $this->fakeGposPaymentGlConfigMasterData();

        $this->json('PUT', '/api/v1/gposPaymentGlConfigMasters/'.$gposPaymentGlConfigMaster->id, $editedGposPaymentGlConfigMaster);

        $this->assertApiResponse($editedGposPaymentGlConfigMaster);
    }

    /**
     * @test
     */
    public function testDeleteGposPaymentGlConfigMaster()
    {
        $gposPaymentGlConfigMaster = $this->makeGposPaymentGlConfigMaster();
        $this->json('DELETE', '/api/v1/gposPaymentGlConfigMasters/'.$gposPaymentGlConfigMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gposPaymentGlConfigMasters/'.$gposPaymentGlConfigMaster->id);

        $this->assertResponseStatus(404);
    }
}
