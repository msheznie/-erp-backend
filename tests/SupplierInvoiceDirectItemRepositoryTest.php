<?php namespace Tests\Repositories;

use App\Models\SupplierInvoiceDirectItem;
use App\Repositories\SupplierInvoiceDirectItemRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierInvoiceDirectItemRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierInvoiceDirectItemRepository
     */
    protected $supplierInvoiceDirectItemRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierInvoiceDirectItemRepo = \App::make(SupplierInvoiceDirectItemRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->make()->toArray();

        $createdSupplierInvoiceDirectItem = $this->supplierInvoiceDirectItemRepo->create($supplierInvoiceDirectItem);

        $createdSupplierInvoiceDirectItem = $createdSupplierInvoiceDirectItem->toArray();
        $this->assertArrayHasKey('id', $createdSupplierInvoiceDirectItem);
        $this->assertNotNull($createdSupplierInvoiceDirectItem['id'], 'Created SupplierInvoiceDirectItem must have id specified');
        $this->assertNotNull(SupplierInvoiceDirectItem::find($createdSupplierInvoiceDirectItem['id']), 'SupplierInvoiceDirectItem with given id must be in DB');
        $this->assertModelData($supplierInvoiceDirectItem, $createdSupplierInvoiceDirectItem);
    }

    /**
     * @test read
     */
    public function test_read_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->create();

        $dbSupplierInvoiceDirectItem = $this->supplierInvoiceDirectItemRepo->find($supplierInvoiceDirectItem->id);

        $dbSupplierInvoiceDirectItem = $dbSupplierInvoiceDirectItem->toArray();
        $this->assertModelData($supplierInvoiceDirectItem->toArray(), $dbSupplierInvoiceDirectItem);
    }

    /**
     * @test update
     */
    public function test_update_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->create();
        $fakeSupplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->make()->toArray();

        $updatedSupplierInvoiceDirectItem = $this->supplierInvoiceDirectItemRepo->update($fakeSupplierInvoiceDirectItem, $supplierInvoiceDirectItem->id);

        $this->assertModelData($fakeSupplierInvoiceDirectItem, $updatedSupplierInvoiceDirectItem->toArray());
        $dbSupplierInvoiceDirectItem = $this->supplierInvoiceDirectItemRepo->find($supplierInvoiceDirectItem->id);
        $this->assertModelData($fakeSupplierInvoiceDirectItem, $dbSupplierInvoiceDirectItem->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_invoice_direct_item()
    {
        $supplierInvoiceDirectItem = factory(SupplierInvoiceDirectItem::class)->create();

        $resp = $this->supplierInvoiceDirectItemRepo->delete($supplierInvoiceDirectItem->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierInvoiceDirectItem::find($supplierInvoiceDirectItem->id), 'SupplierInvoiceDirectItem should not exist in DB');
    }
}
