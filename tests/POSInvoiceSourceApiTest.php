<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSInvoiceSource;

class POSInvoiceSourceApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_invoice_sources', $pOSInvoiceSource
        );

        $this->assertApiResponse($pOSInvoiceSource);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_invoice_sources/'.$pOSInvoiceSource->id
        );

        $this->assertApiResponse($pOSInvoiceSource->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->create();
        $editedPOSInvoiceSource = factory(POSInvoiceSource::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_invoice_sources/'.$pOSInvoiceSource->id,
            $editedPOSInvoiceSource
        );

        $this->assertApiResponse($editedPOSInvoiceSource);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_invoice_source()
    {
        $pOSInvoiceSource = factory(POSInvoiceSource::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_invoice_sources/'.$pOSInvoiceSource->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_invoice_sources/'.$pOSInvoiceSource->id
        );

        $this->response->assertStatus(404);
    }
}
