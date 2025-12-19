<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProcumentOrderApiTest extends TestCase
{
    use MakeProcumentOrderTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateProcumentOrder()
    {
        $procumentOrder = $this->fakeProcumentOrderData();
        $this->json('POST', '/api/v1/procumentOrders', $procumentOrder);

        $this->assertApiResponse($procumentOrder);
    }

    /**
     * @test
     */
    public function testReadProcumentOrder()
    {
        $procumentOrder = $this->makeProcumentOrder();
        $this->json('GET', '/api/v1/procumentOrders/'.$procumentOrder->id);

        $this->assertApiResponse($procumentOrder->toArray());
    }

    /**
     * @test
     */
    public function testUpdateProcumentOrder()
    {
        $procumentOrder = $this->makeProcumentOrder();
        $editedProcumentOrder = $this->fakeProcumentOrderData();

        $this->json('PUT', '/api/v1/procumentOrders/'.$procumentOrder->id, $editedProcumentOrder);

        $this->assertApiResponse($editedProcumentOrder);
    }

    /**
     * @test
     */
    public function testDeleteProcumentOrder()
    {
        $procumentOrder = $this->makeProcumentOrder();
        $this->json('DELETE', '/api/v1/procumentOrders/'.$procumentOrder->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/procumentOrders/'.$procumentOrder->id);

        $this->assertResponseStatus(404);
    }
}
