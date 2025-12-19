<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGInvoiceDetail;

class POSSTAGInvoiceDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_invoice_details', $pOSSTAGInvoiceDetail
        );

        $this->assertApiResponse($pOSSTAGInvoiceDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_invoice_details/'.$pOSSTAGInvoiceDetail->id
        );

        $this->assertApiResponse($pOSSTAGInvoiceDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->create();
        $editedPOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_invoice_details/'.$pOSSTAGInvoiceDetail->id,
            $editedPOSSTAGInvoiceDetail
        );

        $this->assertApiResponse($editedPOSSTAGInvoiceDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_invoice_details/'.$pOSSTAGInvoiceDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_invoice_details/'.$pOSSTAGInvoiceDetail->id
        );

        $this->response->assertStatus(404);
    }
}
