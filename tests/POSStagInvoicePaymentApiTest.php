<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagInvoicePayment;

class POSStagInvoicePaymentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_invoice_payments', $pOSStagInvoicePayment
        );

        $this->assertApiResponse($pOSStagInvoicePayment);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_invoice_payments/'.$pOSStagInvoicePayment->id
        );

        $this->assertApiResponse($pOSStagInvoicePayment->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->create();
        $editedPOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_invoice_payments/'.$pOSStagInvoicePayment->id,
            $editedPOSStagInvoicePayment
        );

        $this->assertApiResponse($editedPOSStagInvoicePayment);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_invoice_payments/'.$pOSStagInvoicePayment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_invoice_payments/'.$pOSStagInvoicePayment->id
        );

        $this->response->assertStatus(404);
    }
}
