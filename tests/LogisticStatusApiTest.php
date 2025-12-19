<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticStatusApiTest extends TestCase
{
    use MakeLogisticStatusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLogisticStatus()
    {
        $logisticStatus = $this->fakeLogisticStatusData();
        $this->json('POST', '/api/v1/logisticStatuses', $logisticStatus);

        $this->assertApiResponse($logisticStatus);
    }

    /**
     * @test
     */
    public function testReadLogisticStatus()
    {
        $logisticStatus = $this->makeLogisticStatus();
        $this->json('GET', '/api/v1/logisticStatuses/'.$logisticStatus->id);

        $this->assertApiResponse($logisticStatus->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLogisticStatus()
    {
        $logisticStatus = $this->makeLogisticStatus();
        $editedLogisticStatus = $this->fakeLogisticStatusData();

        $this->json('PUT', '/api/v1/logisticStatuses/'.$logisticStatus->id, $editedLogisticStatus);

        $this->assertApiResponse($editedLogisticStatus);
    }

    /**
     * @test
     */
    public function testDeleteLogisticStatus()
    {
        $logisticStatus = $this->makeLogisticStatus();
        $this->json('DELETE', '/api/v1/logisticStatuses/'.$logisticStatus->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/logisticStatuses/'.$logisticStatus->id);

        $this->assertResponseStatus(404);
    }
}
