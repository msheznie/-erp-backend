<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvDetailApiTest extends TestCase
{
    use MakeJvDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateJvDetail()
    {
        $jvDetail = $this->fakeJvDetailData();
        $this->json('POST', '/api/v1/jvDetails', $jvDetail);

        $this->assertApiResponse($jvDetail);
    }

    /**
     * @test
     */
    public function testReadJvDetail()
    {
        $jvDetail = $this->makeJvDetail();
        $this->json('GET', '/api/v1/jvDetails/'.$jvDetail->id);

        $this->assertApiResponse($jvDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateJvDetail()
    {
        $jvDetail = $this->makeJvDetail();
        $editedJvDetail = $this->fakeJvDetailData();

        $this->json('PUT', '/api/v1/jvDetails/'.$jvDetail->id, $editedJvDetail);

        $this->assertApiResponse($editedJvDetail);
    }

    /**
     * @test
     */
    public function testDeleteJvDetail()
    {
        $jvDetail = $this->makeJvDetail();
        $this->json('DELETE', '/api/v1/jvDetails/'.$jvDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/jvDetails/'.$jvDetail->id);

        $this->assertResponseStatus(404);
    }
}
