<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogisticDetailsApiTest extends TestCase
{
    use MakeLogisticDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateLogisticDetails()
    {
        $logisticDetails = $this->fakeLogisticDetailsData();
        $this->json('POST', '/api/v1/logisticDetails', $logisticDetails);

        $this->assertApiResponse($logisticDetails);
    }

    /**
     * @test
     */
    public function testReadLogisticDetails()
    {
        $logisticDetails = $this->makeLogisticDetails();
        $this->json('GET', '/api/v1/logisticDetails/'.$logisticDetails->id);

        $this->assertApiResponse($logisticDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateLogisticDetails()
    {
        $logisticDetails = $this->makeLogisticDetails();
        $editedLogisticDetails = $this->fakeLogisticDetailsData();

        $this->json('PUT', '/api/v1/logisticDetails/'.$logisticDetails->id, $editedLogisticDetails);

        $this->assertApiResponse($editedLogisticDetails);
    }

    /**
     * @test
     */
    public function testDeleteLogisticDetails()
    {
        $logisticDetails = $this->makeLogisticDetails();
        $this->json('DELETE', '/api/v1/logisticDetails/'.$logisticDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/logisticDetails/'.$logisticDetails->id);

        $this->assertResponseStatus(404);
    }
}
