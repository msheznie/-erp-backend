<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposInvoiceDetailApiTest extends TestCase
{
    use MakeGposInvoiceDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->fakeGposInvoiceDetailData();
        $this->json('POST', '/api/v1/gposInvoiceDetails', $gposInvoiceDetail);

        $this->assertApiResponse($gposInvoiceDetail);
    }

    /**
     * @test
     */
    public function testReadGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->makeGposInvoiceDetail();
        $this->json('GET', '/api/v1/gposInvoiceDetails/'.$gposInvoiceDetail->id);

        $this->assertApiResponse($gposInvoiceDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->makeGposInvoiceDetail();
        $editedGposInvoiceDetail = $this->fakeGposInvoiceDetailData();

        $this->json('PUT', '/api/v1/gposInvoiceDetails/'.$gposInvoiceDetail->id, $editedGposInvoiceDetail);

        $this->assertApiResponse($editedGposInvoiceDetail);
    }

    /**
     * @test
     */
    public function testDeleteGposInvoiceDetail()
    {
        $gposInvoiceDetail = $this->makeGposInvoiceDetail();
        $this->json('DELETE', '/api/v1/gposInvoiceDetails/'.$gposInvoiceDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gposInvoiceDetails/'.$gposInvoiceDetail->id);

        $this->assertResponseStatus(404);
    }
}
