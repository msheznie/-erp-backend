<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticShippingStatusApiTest extends TestCase
{
    use MakeLogisticShippingStatusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->fakeLogisticShippingStatusData();
        $this->json('POST', '/api/v1/logisticShippingStatuses', $logisticShippingStatus);

        $this->assertApiResponse($logisticShippingStatus);
    }

    /**
     * @test
     */
    public function testReadLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->makeLogisticShippingStatus();
        $this->json('GET', '/api/v1/logisticShippingStatuses/'.$logisticShippingStatus->id);

        $this->assertApiResponse($logisticShippingStatus->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->makeLogisticShippingStatus();
        $editedLogisticShippingStatus = $this->fakeLogisticShippingStatusData();

        $this->json('PUT', '/api/v1/logisticShippingStatuses/'.$logisticShippingStatus->id, $editedLogisticShippingStatus);

        $this->assertApiResponse($editedLogisticShippingStatus);
    }

    /**
     * @test
     */
    public function testDeleteLogisticShippingStatus()
    {
        $logisticShippingStatus = $this->makeLogisticShippingStatus();
        $this->json('DELETE', '/api/v1/logisticShippingStatuses/'.$logisticShippingStatus->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/logisticShippingStatuses/'.$logisticShippingStatus->id);

        $this->assertResponseStatus(404);
    }
}
