<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticShippingModeApiTest extends TestCase
{
    use MakeLogisticShippingModeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLogisticShippingMode()
    {
        $logisticShippingMode = $this->fakeLogisticShippingModeData();
        $this->json('POST', '/api/v1/logisticShippingModes', $logisticShippingMode);

        $this->assertApiResponse($logisticShippingMode);
    }

    /**
     * @test
     */
    public function testReadLogisticShippingMode()
    {
        $logisticShippingMode = $this->makeLogisticShippingMode();
        $this->json('GET', '/api/v1/logisticShippingModes/'.$logisticShippingMode->id);

        $this->assertApiResponse($logisticShippingMode->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLogisticShippingMode()
    {
        $logisticShippingMode = $this->makeLogisticShippingMode();
        $editedLogisticShippingMode = $this->fakeLogisticShippingModeData();

        $this->json('PUT', '/api/v1/logisticShippingModes/'.$logisticShippingMode->id, $editedLogisticShippingMode);

        $this->assertApiResponse($editedLogisticShippingMode);
    }

    /**
     * @test
     */
    public function testDeleteLogisticShippingMode()
    {
        $logisticShippingMode = $this->makeLogisticShippingMode();
        $this->json('DELETE', '/api/v1/logisticShippingModes/'.$logisticShippingMode->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/logisticShippingModes/'.$logisticShippingMode->id);

        $this->assertResponseStatus(404);
    }
}
