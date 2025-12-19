<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProcumentOrderDetailApiTest extends TestCase
{
    use MakeProcumentOrderDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->fakeProcumentOrderDetailData();
        $this->json('POST', '/api/v1/procumentOrderDetails', $procumentOrderDetail);

        $this->assertApiResponse($procumentOrderDetail);
    }

    /**
     * @test
     */
    public function testReadProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->makeProcumentOrderDetail();
        $this->json('GET', '/api/v1/procumentOrderDetails/'.$procumentOrderDetail->id);

        $this->assertApiResponse($procumentOrderDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->makeProcumentOrderDetail();
        $editedProcumentOrderDetail = $this->fakeProcumentOrderDetailData();

        $this->json('PUT', '/api/v1/procumentOrderDetails/'.$procumentOrderDetail->id, $editedProcumentOrderDetail);

        $this->assertApiResponse($editedProcumentOrderDetail);
    }

    /**
     * @test
     */
    public function testDeleteProcumentOrderDetail()
    {
        $procumentOrderDetail = $this->makeProcumentOrderDetail();
        $this->json('DELETE', '/api/v1/procumentOrderDetails/'.$procumentOrderDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/procumentOrderDetails/'.$procumentOrderDetail->id);

        $this->assertResponseStatus(404);
    }
}
