<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AlertApiTest extends TestCase
{
    use MakeAlertTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAlert()
    {
        $alert = $this->fakeAlertData();
        $this->json('POST', '/api/v1/alerts', $alert);

        $this->assertApiResponse($alert);
    }

    /**
     * @test
     */
    public function testReadAlert()
    {
        $alert = $this->makeAlert();
        $this->json('GET', '/api/v1/alerts/'.$alert->id);

        $this->assertApiResponse($alert->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAlert()
    {
        $alert = $this->makeAlert();
        $editedAlert = $this->fakeAlertData();

        $this->json('PUT', '/api/v1/alerts/'.$alert->id, $editedAlert);

        $this->assertApiResponse($editedAlert);
    }

    /**
     * @test
     */
    public function testDeleteAlert()
    {
        $alert = $this->makeAlert();
        $this->json('DELETE', '/api/v1/alerts/'.$alert->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/alerts/'.$alert->id);

        $this->assertResponseStatus(404);
    }
}
