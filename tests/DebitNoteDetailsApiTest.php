<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteDetailsApiTest extends TestCase
{
    use MakeDebitNoteDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDebitNoteDetails()
    {
        $debitNoteDetails = $this->fakeDebitNoteDetailsData();
        $this->json('POST', '/api/v1/debitNoteDetails', $debitNoteDetails);

        $this->assertApiResponse($debitNoteDetails);
    }

    /**
     * @test
     */
    public function testReadDebitNoteDetails()
    {
        $debitNoteDetails = $this->makeDebitNoteDetails();
        $this->json('GET', '/api/v1/debitNoteDetails/'.$debitNoteDetails->id);

        $this->assertApiResponse($debitNoteDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDebitNoteDetails()
    {
        $debitNoteDetails = $this->makeDebitNoteDetails();
        $editedDebitNoteDetails = $this->fakeDebitNoteDetailsData();

        $this->json('PUT', '/api/v1/debitNoteDetails/'.$debitNoteDetails->id, $editedDebitNoteDetails);

        $this->assertApiResponse($editedDebitNoteDetails);
    }

    /**
     * @test
     */
    public function testDeleteDebitNoteDetails()
    {
        $debitNoteDetails = $this->makeDebitNoteDetails();
        $this->json('DELETE', '/api/v1/debitNoteDetails/'.$debitNoteDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/debitNoteDetails/'.$debitNoteDetails->id);

        $this->assertResponseStatus(404);
    }
}
