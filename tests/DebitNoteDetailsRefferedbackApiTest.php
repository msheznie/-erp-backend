<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteDetailsRefferedbackApiTest extends TestCase
{
    use MakeDebitNoteDetailsRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->fakeDebitNoteDetailsRefferedbackData();
        $this->json('POST', '/api/v1/debitNoteDetailsRefferedbacks', $debitNoteDetailsRefferedback);

        $this->assertApiResponse($debitNoteDetailsRefferedback);
    }

    /**
     * @test
     */
    public function testReadDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->makeDebitNoteDetailsRefferedback();
        $this->json('GET', '/api/v1/debitNoteDetailsRefferedbacks/'.$debitNoteDetailsRefferedback->id);

        $this->assertApiResponse($debitNoteDetailsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->makeDebitNoteDetailsRefferedback();
        $editedDebitNoteDetailsRefferedback = $this->fakeDebitNoteDetailsRefferedbackData();

        $this->json('PUT', '/api/v1/debitNoteDetailsRefferedbacks/'.$debitNoteDetailsRefferedback->id, $editedDebitNoteDetailsRefferedback);

        $this->assertApiResponse($editedDebitNoteDetailsRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteDebitNoteDetailsRefferedback()
    {
        $debitNoteDetailsRefferedback = $this->makeDebitNoteDetailsRefferedback();
        $this->json('DELETE', '/api/v1/debitNoteDetailsRefferedbacks/'.$debitNoteDetailsRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/debitNoteDetailsRefferedbacks/'.$debitNoteDetailsRefferedback->id);

        $this->assertResponseStatus(404);
    }
}
