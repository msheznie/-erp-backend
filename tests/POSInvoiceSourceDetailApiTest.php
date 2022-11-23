<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSInvoiceSourceDetail;

class POSInvoiceSourceDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_invoice_source_details', $pOSInvoiceSourceDetail
        );

        $this->assertApiResponse($pOSInvoiceSourceDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_invoice_source_details/'.$pOSInvoiceSourceDetail->id
        );

        $this->assertApiResponse($pOSInvoiceSourceDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->create();
        $editedPOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_invoice_source_details/'.$pOSInvoiceSourceDetail->id,
            $editedPOSInvoiceSourceDetail
        );

        $this->assertApiResponse($editedPOSInvoiceSourceDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_invoice_source_details/'.$pOSInvoiceSourceDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_invoice_source_details/'.$pOSInvoiceSourceDetail->id
        );

        $this->response->assertStatus(404);
    }
}
