<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreditNoteDetailsApiTest extends TestCase
{
    use MakeCreditNoteDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCreditNoteDetails()
    {
        $creditNoteDetails = $this->fakeCreditNoteDetailsData();
        $this->json('POST', '/api/v1/creditNoteDetails', $creditNoteDetails);

        $this->assertApiResponse($creditNoteDetails);
    }

    /**
     * @test
     */
    public function testReadCreditNoteDetails()
    {
        $creditNoteDetails = $this->makeCreditNoteDetails();
        $this->json('GET', '/api/v1/creditNoteDetails/'.$creditNoteDetails->id);

        $this->assertApiResponse($creditNoteDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCreditNoteDetails()
    {
        $creditNoteDetails = $this->makeCreditNoteDetails();
        $editedCreditNoteDetails = $this->fakeCreditNoteDetailsData();

        $this->json('PUT', '/api/v1/creditNoteDetails/'.$creditNoteDetails->id, $editedCreditNoteDetails);

        $this->assertApiResponse($editedCreditNoteDetails);
    }

    /**
     * @test
     */
    public function testDeleteCreditNoteDetails()
    {
        $creditNoteDetails = $this->makeCreditNoteDetails();
        $this->json('DELETE', '/api/v1/creditNoteDetails/'.$creditNoteDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/creditNoteDetails/'.$creditNoteDetails->id);

        $this->assertResponseStatus(404);
    }
}
