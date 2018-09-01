<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectReceiptDetailApiTest extends TestCase
{
    use MakeDirectReceiptDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDirectReceiptDetail()
    {
        $directReceiptDetail = $this->fakeDirectReceiptDetailData();
        $this->json('POST', '/api/v1/directReceiptDetails', $directReceiptDetail);

        $this->assertApiResponse($directReceiptDetail);
    }

    /**
     * @test
     */
    public function testReadDirectReceiptDetail()
    {
        $directReceiptDetail = $this->makeDirectReceiptDetail();
        $this->json('GET', '/api/v1/directReceiptDetails/'.$directReceiptDetail->id);

        $this->assertApiResponse($directReceiptDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDirectReceiptDetail()
    {
        $directReceiptDetail = $this->makeDirectReceiptDetail();
        $editedDirectReceiptDetail = $this->fakeDirectReceiptDetailData();

        $this->json('PUT', '/api/v1/directReceiptDetails/'.$directReceiptDetail->id, $editedDirectReceiptDetail);

        $this->assertApiResponse($editedDirectReceiptDetail);
    }

    /**
     * @test
     */
    public function testDeleteDirectReceiptDetail()
    {
        $directReceiptDetail = $this->makeDirectReceiptDetail();
        $this->json('DELETE', '/api/v1/directReceiptDetails/'.$directReceiptDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/directReceiptDetails/'.$directReceiptDetail->id);

        $this->assertResponseStatus(404);
    }
}
