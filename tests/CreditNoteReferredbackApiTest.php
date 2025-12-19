<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteReferredbackApiTest extends TestCase
{
    use MakeCreditNoteReferredbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->fakeCreditNoteReferredbackData();
        $this->json('POST', '/api/v1/creditNoteReferredbacks', $creditNoteReferredback);

        $this->assertApiResponse($creditNoteReferredback);
    }

    /**
     * @test
     */
    public function testReadCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->makeCreditNoteReferredback();
        $this->json('GET', '/api/v1/creditNoteReferredbacks/'.$creditNoteReferredback->id);

        $this->assertApiResponse($creditNoteReferredback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->makeCreditNoteReferredback();
        $editedCreditNoteReferredback = $this->fakeCreditNoteReferredbackData();

        $this->json('PUT', '/api/v1/creditNoteReferredbacks/'.$creditNoteReferredback->id, $editedCreditNoteReferredback);

        $this->assertApiResponse($editedCreditNoteReferredback);
    }

    /**
     * @test
     */
    public function testDeleteCreditNoteReferredback()
    {
        $creditNoteReferredback = $this->makeCreditNoteReferredback();
        $this->json('DELETE', '/api/v1/creditNoteReferredbacks/'.$creditNoteReferredback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/creditNoteReferredbacks/'.$creditNoteReferredback->id);

        $this->assertResponseStatus(404);
    }
}
