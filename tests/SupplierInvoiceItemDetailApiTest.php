<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierInvoiceItemDetail;

class SupplierInvoiceItemDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_invoice_item_details', $supplierInvoiceItemDetail
        );

        $this->assertApiResponse($supplierInvoiceItemDetail);
    }

    /**
     * @test
     */
    public function test_read_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_invoice_item_details/'.$supplierInvoiceItemDetail->id
        );

        $this->assertApiResponse($supplierInvoiceItemDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->create();
        $editedSupplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_invoice_item_details/'.$supplierInvoiceItemDetail->id,
            $editedSupplierInvoiceItemDetail
        );

        $this->assertApiResponse($editedSupplierInvoiceItemDetail);
    }

    /**
     * @test
     */
    public function test_delete_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_invoice_item_details/'.$supplierInvoiceItemDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_invoice_item_details/'.$supplierInvoiceItemDetail->id
        );

        $this->response->assertStatus(404);
    }
}
