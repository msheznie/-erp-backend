<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CreditNoteReceipt;

class CreditNoteReceiptApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/credit_note_receipts', $creditNoteReceipt
        );

        $this->assertApiResponse($creditNoteReceipt);
    }

    /**
     * @test
     */
    public function test_read_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/credit_note_receipts/'.$creditNoteReceipt->id
        );

        $this->assertApiResponse($creditNoteReceipt->toArray());
    }

    /**
     * @test
     */
    public function test_update_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->create();
        $editedCreditNoteReceipt = factory(CreditNoteReceipt::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/credit_note_receipts/'.$creditNoteReceipt->id,
            $editedCreditNoteReceipt
        );

        $this->assertApiResponse($editedCreditNoteReceipt);
    }

    /**
     * @test
     */
    public function test_delete_credit_note_receipt()
    {
        $creditNoteReceipt = factory(CreditNoteReceipt::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/credit_note_receipts/'.$creditNoteReceipt->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/credit_note_receipts/'.$creditNoteReceipt->id
        );

        $this->response->assertStatus(404);
    }
}
