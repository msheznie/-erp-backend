<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceInvoicePayment;

class POSSourceInvoicePaymentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_invoice_payments', $pOSSourceInvoicePayment
        );

        $this->assertApiResponse($pOSSourceInvoicePayment);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_invoice_payments/'.$pOSSourceInvoicePayment->id
        );

        $this->assertApiResponse($pOSSourceInvoicePayment->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->create();
        $editedPOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_invoice_payments/'.$pOSSourceInvoicePayment->id,
            $editedPOSSourceInvoicePayment
        );

        $this->assertApiResponse($editedPOSSourceInvoicePayment);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_invoice_payments/'.$pOSSourceInvoicePayment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_invoice_payments/'.$pOSSourceInvoicePayment->id
        );

        $this->response->assertStatus(404);
    }
}
