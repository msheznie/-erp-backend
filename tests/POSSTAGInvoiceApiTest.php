<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGInvoice;

class POSSTAGInvoiceApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_invoices', $pOSSTAGInvoice
        );

        $this->assertApiResponse($pOSSTAGInvoice);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_invoices/'.$pOSSTAGInvoice->id
        );

        $this->assertApiResponse($pOSSTAGInvoice->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->create();
        $editedPOSSTAGInvoice = factory(POSSTAGInvoice::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_invoices/'.$pOSSTAGInvoice->id,
            $editedPOSSTAGInvoice
        );

        $this->assertApiResponse($editedPOSSTAGInvoice);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_invoices/'.$pOSSTAGInvoice->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_invoices/'.$pOSSTAGInvoice->id
        );

        $this->response->assertStatus(404);
    }
}
