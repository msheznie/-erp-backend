<?php namespace Tests\Repositories;

use App\Models\SupplierInvoiceItemDetail;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierInvoiceItemDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierInvoiceItemDetailRepository
     */
    protected $supplierInvoiceItemDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierInvoiceItemDetailRepo = \App::make(SupplierInvoiceItemDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->make()->toArray();

        $createdSupplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepo->create($supplierInvoiceItemDetail);

        $createdSupplierInvoiceItemDetail = $createdSupplierInvoiceItemDetail->toArray();
        $this->assertArrayHasKey('id', $createdSupplierInvoiceItemDetail);
        $this->assertNotNull($createdSupplierInvoiceItemDetail['id'], 'Created SupplierInvoiceItemDetail must have id specified');
        $this->assertNotNull(SupplierInvoiceItemDetail::find($createdSupplierInvoiceItemDetail['id']), 'SupplierInvoiceItemDetail with given id must be in DB');
        $this->assertModelData($supplierInvoiceItemDetail, $createdSupplierInvoiceItemDetail);
    }

    /**
     * @test read
     */
    public function test_read_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->create();

        $dbSupplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepo->find($supplierInvoiceItemDetail->id);

        $dbSupplierInvoiceItemDetail = $dbSupplierInvoiceItemDetail->toArray();
        $this->assertModelData($supplierInvoiceItemDetail->toArray(), $dbSupplierInvoiceItemDetail);
    }

    /**
     * @test update
     */
    public function test_update_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->create();
        $fakeSupplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->make()->toArray();

        $updatedSupplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepo->update($fakeSupplierInvoiceItemDetail, $supplierInvoiceItemDetail->id);

        $this->assertModelData($fakeSupplierInvoiceItemDetail, $updatedSupplierInvoiceItemDetail->toArray());
        $dbSupplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepo->find($supplierInvoiceItemDetail->id);
        $this->assertModelData($fakeSupplierInvoiceItemDetail, $dbSupplierInvoiceItemDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_invoice_item_detail()
    {
        $supplierInvoiceItemDetail = factory(SupplierInvoiceItemDetail::class)->create();

        $resp = $this->supplierInvoiceItemDetailRepo->delete($supplierInvoiceItemDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierInvoiceItemDetail::find($supplierInvoiceItemDetail->id), 'SupplierInvoiceItemDetail should not exist in DB');
    }
}
