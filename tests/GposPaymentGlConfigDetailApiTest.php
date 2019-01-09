<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposPaymentGlConfigDetailApiTest extends TestCase
{
    use MakeGposPaymentGlConfigDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->fakeGposPaymentGlConfigDetailData();
        $this->json('POST', '/api/v1/gposPaymentGlConfigDetails', $gposPaymentGlConfigDetail);

        $this->assertApiResponse($gposPaymentGlConfigDetail);
    }

    /**
     * @test
     */
    public function testReadGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->makeGposPaymentGlConfigDetail();
        $this->json('GET', '/api/v1/gposPaymentGlConfigDetails/'.$gposPaymentGlConfigDetail->id);

        $this->assertApiResponse($gposPaymentGlConfigDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->makeGposPaymentGlConfigDetail();
        $editedGposPaymentGlConfigDetail = $this->fakeGposPaymentGlConfigDetailData();

        $this->json('PUT', '/api/v1/gposPaymentGlConfigDetails/'.$gposPaymentGlConfigDetail->id, $editedGposPaymentGlConfigDetail);

        $this->assertApiResponse($editedGposPaymentGlConfigDetail);
    }

    /**
     * @test
     */
    public function testDeleteGposPaymentGlConfigDetail()
    {
        $gposPaymentGlConfigDetail = $this->makeGposPaymentGlConfigDetail();
        $this->json('DELETE', '/api/v1/gposPaymentGlConfigDetails/'.$gposPaymentGlConfigDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gposPaymentGlConfigDetails/'.$gposPaymentGlConfigDetail->id);

        $this->assertResponseStatus(404);
    }
}
