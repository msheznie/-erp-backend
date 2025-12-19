<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteApiTest extends TestCase
{
    use MakeCreditNoteTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCreditNote()
    {
        $creditNote = $this->fakeCreditNoteData();
        $this->json('POST', '/api/v1/creditNotes', $creditNote);

        $this->assertApiResponse($creditNote);
    }

    /**
     * @test
     */
    public function testReadCreditNote()
    {
        $creditNote = $this->makeCreditNote();
        $this->json('GET', '/api/v1/creditNotes/'.$creditNote->id);

        $this->assertApiResponse($creditNote->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCreditNote()
    {
        $creditNote = $this->makeCreditNote();
        $editedCreditNote = $this->fakeCreditNoteData();

        $this->json('PUT', '/api/v1/creditNotes/'.$creditNote->id, $editedCreditNote);

        $this->assertApiResponse($editedCreditNote);
    }

    /**
     * @test
     */
    public function testDeleteCreditNote()
    {
        $creditNote = $this->makeCreditNote();
        $this->json('DELETE', '/api/v1/creditNotes/'.$creditNote->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/creditNotes/'.$creditNote->id);

        $this->assertResponseStatus(404);
    }
}
