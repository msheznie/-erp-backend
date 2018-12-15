<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrvMasterRefferedbackApiTest extends TestCase
{
    use MakeGrvMasterRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->fakeGrvMasterRefferedbackData();
        $this->json('POST', '/api/v1/grvMasterRefferedbacks', $grvMasterRefferedback);

        $this->assertApiResponse($grvMasterRefferedback);
    }

    /**
     * @test
     */
    public function testReadGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->makeGrvMasterRefferedback();
        $this->json('GET', '/api/v1/grvMasterRefferedbacks/'.$grvMasterRefferedback->id);

        $this->assertApiResponse($grvMasterRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->makeGrvMasterRefferedback();
        $editedGrvMasterRefferedback = $this->fakeGrvMasterRefferedbackData();

        $this->json('PUT', '/api/v1/grvMasterRefferedbacks/'.$grvMasterRefferedback->id, $editedGrvMasterRefferedback);

        $this->assertApiResponse($editedGrvMasterRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->makeGrvMasterRefferedback();
        $this->json('DELETE', '/api/v1/grvMasterRefferedbacks/'.$grvMasterRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/grvMasterRefferedbacks/'.$grvMasterRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
