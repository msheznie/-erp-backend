<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticApiTest extends TestCase
{
    use MakeLogisticTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLogistic()
    {
        $logistic = $this->fakeLogisticData();
        $this->json('POST', '/api/v1/logistics', $logistic);

        $this->assertApiResponse($logistic);
    }

    /**
     * @test
     */
    public function testReadLogistic()
    {
        $logistic = $this->makeLogistic();
        $this->json('GET', '/api/v1/logistics/'.$logistic->id);

        $this->assertApiResponse($logistic->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLogistic()
    {
        $logistic = $this->makeLogistic();
        $editedLogistic = $this->fakeLogisticData();

        $this->json('PUT', '/api/v1/logistics/'.$logistic->id, $editedLogistic);

        $this->assertApiResponse($editedLogistic);
    }

    /**
     * @test
     */
    public function testDeleteLogistic()
    {
        $logistic = $this->makeLogistic();
        $this->json('DELETE', '/api/v1/logistics/'.$logistic->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/logistics/'.$logistic->id);

        $this->assertResponseStatus(404);
    }
}
