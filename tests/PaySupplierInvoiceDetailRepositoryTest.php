<?php

use App\Models\PaySupplierInvoiceDetail;
use App\Repositories\PaySupplierInvoiceDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceDetailRepositoryTest extends TestCase
{
    use MakePaySupplierInvoiceDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaySupplierInvoiceDetailRepository
     */
    protected $paySupplierInvoiceDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paySupplierInvoiceDetailRepo = App::make(PaySupplierInvoiceDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->fakePaySupplierInvoiceDetailData();
        $createdPaySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepo->create($paySupplierInvoiceDetail);
        $createdPaySupplierInvoiceDetail = $createdPaySupplierInvoiceDetail->toArray();
        $this->assertArrayHasKey('id', $createdPaySupplierInvoiceDetail);
        $this->assertNotNull($createdPaySupplierInvoiceDetail['id'], 'Created PaySupplierInvoiceDetail must have id specified');
        $this->assertNotNull(PaySupplierInvoiceDetail::find($createdPaySupplierInvoiceDetail['id']), 'PaySupplierInvoiceDetail with given id must be in DB');
        $this->assertModelData($paySupplierInvoiceDetail, $createdPaySupplierInvoiceDetail);
    }

    /**
     * @test read
     */
    public function testReadPaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->makePaySupplierInvoiceDetail();
        $dbPaySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepo->find($paySupplierInvoiceDetail->id);
        $dbPaySupplierInvoiceDetail = $dbPaySupplierInvoiceDetail->toArray();
        $this->assertModelData($paySupplierInvoiceDetail->toArray(), $dbPaySupplierInvoiceDetail);
    }

    /**
     * @test update
     */
    public function testUpdatePaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->makePaySupplierInvoiceDetail();
        $fakePaySupplierInvoiceDetail = $this->fakePaySupplierInvoiceDetailData();
        $updatedPaySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepo->update($fakePaySupplierInvoiceDetail, $paySupplierInvoiceDetail->id);
        $this->assertModelData($fakePaySupplierInvoiceDetail, $updatedPaySupplierInvoiceDetail->toArray());
        $dbPaySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepo->find($paySupplierInvoiceDetail->id);
        $this->assertModelData($fakePaySupplierInvoiceDetail, $dbPaySupplierInvoiceDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->makePaySupplierInvoiceDetail();
        $resp = $this->paySupplierInvoiceDetailRepo->delete($paySupplierInvoiceDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(PaySupplierInvoiceDetail::find($paySupplierInvoiceDetail->id), 'PaySupplierInvoiceDetail should not exist in DB');
    }
}
