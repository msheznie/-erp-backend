<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteDetailsRefferdbackApiTest extends TestCase
{
    use MakeCreditNoteDetailsRefferdbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->fakeCreditNoteDetailsRefferdbackData();
        $this->json('POST', '/api/v1/creditNoteDetailsRefferdbacks', $creditNoteDetailsRefferdback);

        $this->assertApiResponse($creditNoteDetailsRefferdback);
    }

    /**
     * @test
     */
    public function testReadCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->makeCreditNoteDetailsRefferdback();
        $this->json('GET', '/api/v1/creditNoteDetailsRefferdbacks/'.$creditNoteDetailsRefferdback->id);

        $this->assertApiResponse($creditNoteDetailsRefferdback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->makeCreditNoteDetailsRefferdback();
        $editedCreditNoteDetailsRefferdback = $this->fakeCreditNoteDetailsRefferdbackData();

        $this->json('PUT', '/api/v1/creditNoteDetailsRefferdbacks/'.$creditNoteDetailsRefferdback->id, $editedCreditNoteDetailsRefferdback);

        $this->assertApiResponse($editedCreditNoteDetailsRefferdback);
    }

    /**
     * @test
     */
    public function testDeleteCreditNoteDetailsRefferdback()
    {
        $creditNoteDetailsRefferdback = $this->makeCreditNoteDetailsRefferdback();
        $this->json('DELETE', '/api/v1/creditNoteDetailsRefferdbacks/'.$creditNoteDetailsRefferdback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/creditNoteDetailsRefferdbacks/'.$creditNoteDetailsRefferdback->id);

        $this->assertResponseStatus(404);
    }
}
