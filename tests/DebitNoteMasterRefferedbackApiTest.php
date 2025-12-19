<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteMasterRefferedbackApiTest extends TestCase
{
    use MakeDebitNoteMasterRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->fakeDebitNoteMasterRefferedbackData();
        $this->json('POST', '/api/v1/debitNoteMasterRefferedbacks', $debitNoteMasterRefferedback);

        $this->assertApiResponse($debitNoteMasterRefferedback);
    }

    /**
     * @test
     */
    public function testReadDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->makeDebitNoteMasterRefferedback();
        $this->json('GET', '/api/v1/debitNoteMasterRefferedbacks/'.$debitNoteMasterRefferedback->id);

        $this->assertApiResponse($debitNoteMasterRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->makeDebitNoteMasterRefferedback();
        $editedDebitNoteMasterRefferedback = $this->fakeDebitNoteMasterRefferedbackData();

        $this->json('PUT', '/api/v1/debitNoteMasterRefferedbacks/'.$debitNoteMasterRefferedback->id, $editedDebitNoteMasterRefferedback);

        $this->assertApiResponse($editedDebitNoteMasterRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteDebitNoteMasterRefferedback()
    {
        $debitNoteMasterRefferedback = $this->makeDebitNoteMasterRefferedback();
        $this->json('DELETE', '/api/v1/debitNoteMasterRefferedbacks/'.$debitNoteMasterRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/debitNoteMasterRefferedbacks/'.$debitNoteMasterRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
