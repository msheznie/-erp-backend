<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierInvoiceDirectItem;

class SupplierInvoiceDirectItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_invoice_direct_items', $supplierInvoiceDirectItem
        );

        $this->assertApiResponse($supplierInvoiceDirectItem);
    }

    /**
     * @test
     */
    public function test_read_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_invoice_direct_items/'.$supplierInvoiceDirectItem->id
        );

        $this->assertApiResponse($supplierInvoiceDirectItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->create();
        $editedSupplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_invoice_direct_items/'.$supplierInvoiceDirectItem->id,
            $editedSupplierInvoiceDirectItem
        );

        $this->assertApiResponse($editedSupplierInvoiceDirectItem);
    }

    /**
     * @test
     */
    public function test_delete_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_invoice_direct_items/'.$supplierInvoiceDirectItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_invoice_direct_items/'.$supplierInvoiceDirectItem->id
        );

        $this->response->assertStatus(404);
    }
}
