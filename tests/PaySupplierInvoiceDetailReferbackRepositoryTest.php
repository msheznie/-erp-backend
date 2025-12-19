<?php

use App\Models\PaySupplierInvoiceDetailReferback;
use App\Repositories\PaySupplierInvoiceDetailReferbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceDetailReferbackRepositoryTest extends TestCase
{
    use MakePaySupplierInvoiceDetailReferbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaySupplierInvoiceDetailReferbackRepository
     */
    protected $paySupplierInvoiceDetailReferbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paySupplierInvoiceDetailReferbackRepo = App::make(PaySupplierInvoiceDetailReferbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->fakePaySupplierInvoiceDetailReferbackData();
        $createdPaySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepo->create($paySupplierInvoiceDetailReferback);
        $createdPaySupplierInvoiceDetailReferback = $createdPaySupplierInvoiceDetailReferback->toArray();
        $this->assertArrayHasKey('id', $createdPaySupplierInvoiceDetailReferback);
        $this->assertNotNull($createdPaySupplierInvoiceDetailReferback['id'], 'Created PaySupplierInvoiceDetailReferback must have id specified');
        $this->assertNotNull(PaySupplierInvoiceDetailReferback::find($createdPaySupplierInvoiceDetailReferback['id']), 'PaySupplierInvoiceDetailReferback with given id must be in DB');
        $this->assertModelData($paySupplierInvoiceDetailReferback, $createdPaySupplierInvoiceDetailReferback);
    }

    /**
     * @test read
     */
    public function testReadPaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->makePaySupplierInvoiceDetailReferback();
        $dbPaySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepo->find($paySupplierInvoiceDetailReferback->id);
        $dbPaySupplierInvoiceDetailReferback = $dbPaySupplierInvoiceDetailReferback->toArray();
        $this->assertModelData($paySupplierInvoiceDetailReferback->toArray(), $dbPaySupplierInvoiceDetailReferback);
    }

    /**
     * @test update
     */
    public function testUpdatePaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->makePaySupplierInvoiceDetailReferback();
        $fakePaySupplierInvoiceDetailReferback = $this->fakePaySupplierInvoiceDetailReferbackData();
        $updatedPaySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepo->update($fakePaySupplierInvoiceDetailReferback, $paySupplierInvoiceDetailReferback->id);
        $this->assertModelData($fakePaySupplierInvoiceDetailReferback, $updatedPaySupplierInvoiceDetailReferback->toArray());
        $dbPaySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepo->find($paySupplierInvoiceDetailReferback->id);
        $this->assertModelData($fakePaySupplierInvoiceDetailReferback, $dbPaySupplierInvoiceDetailReferback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->makePaySupplierInvoiceDetailReferback();
        $resp = $this->paySupplierInvoiceDetailReferbackRepo->delete($paySupplierInvoiceDetailReferback->id);
        $this->assertTrue($resp);
        $this->assertNull(PaySupplierInvoiceDetailReferback::find($paySupplierInvoiceDetailReferback->id), 'PaySupplierInvoiceDetailReferback should not exist in DB');
    }
}
