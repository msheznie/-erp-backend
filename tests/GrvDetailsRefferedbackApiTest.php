<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrvDetailsRefferedbackApiTest extends TestCase
{
    use MakeGrvDetailsRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->fakeGrvDetailsRefferedbackData();
        $this->json('POST', '/api/v1/grvDetailsRefferedbacks', $grvDetailsRefferedback);

        $this->assertApiResponse($grvDetailsRefferedback);
    }

    /**
     * @test
     */
    public function testReadGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->makeGrvDetailsRefferedback();
        $this->json('GET', '/api/v1/grvDetailsRefferedbacks/'.$grvDetailsRefferedback->id);

        $this->assertApiResponse($grvDetailsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->makeGrvDetailsRefferedback();
        $editedGrvDetailsRefferedback = $this->fakeGrvDetailsRefferedbackData();

        $this->json('PUT', '/api/v1/grvDetailsRefferedbacks/'.$grvDetailsRefferedback->id, $editedGrvDetailsRefferedback);

        $this->assertApiResponse($editedGrvDetailsRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->makeGrvDetailsRefferedback();
        $this->json('DELETE', '/api/v1/grvDetailsRefferedbacks/'.$grvDetailsRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/grvDetailsRefferedbacks/'.$grvDetailsRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
