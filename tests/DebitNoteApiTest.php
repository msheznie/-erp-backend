<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DebitNoteApiTest extends TestCase
{
    use MakeDebitNoteTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDebitNote()
    {
        $debitNote = $this->fakeDebitNoteData();
        $this->json('POST', '/api/v1/debitNotes', $debitNote);

        $this->assertApiResponse($debitNote);
    }

    /**
     * @test
     */
    public function testReadDebitNote()
    {
        $debitNote = $this->makeDebitNote();
        $this->json('GET', '/api/v1/debitNotes/'.$debitNote->id);

        $this->assertApiResponse($debitNote->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDebitNote()
    {
        $debitNote = $this->makeDebitNote();
        $editedDebitNote = $this->fakeDebitNoteData();

        $this->json('PUT', '/api/v1/debitNotes/'.$debitNote->id, $editedDebitNote);

        $this->assertApiResponse($editedDebitNote);
    }

    /**
     * @test
     */
    public function testDeleteDebitNote()
    {
        $debitNote = $this->makeDebitNote();
        $this->json('DELETE', '/api/v1/debitNotes/'.$debitNote->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/debitNotes/'.$debitNote->id);

        $this->assertResponseStatus(404);
    }
}
