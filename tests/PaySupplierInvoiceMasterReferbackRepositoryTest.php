<?php

use App\Models\PaySupplierInvoiceMasterReferback;
use App\Repositories\PaySupplierInvoiceMasterReferbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceMasterReferbackRepositoryTest extends TestCase
{
    use MakePaySupplierInvoiceMasterReferbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaySupplierInvoiceMasterReferbackRepository
     */
    protected $paySupplierInvoiceMasterReferbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paySupplierInvoiceMasterReferbackRepo = App::make(PaySupplierInvoiceMasterReferbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->fakePaySupplierInvoiceMasterReferbackData();
        $createdPaySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepo->create($paySupplierInvoiceMasterReferback);
        $createdPaySupplierInvoiceMasterReferback = $createdPaySupplierInvoiceMasterReferback->toArray();
        $this->assertArrayHasKey('id', $createdPaySupplierInvoiceMasterReferback);
        $this->assertNotNull($createdPaySupplierInvoiceMasterReferback['id'], 'Created PaySupplierInvoiceMasterReferback must have id specified');
        $this->assertNotNull(PaySupplierInvoiceMasterReferback::find($createdPaySupplierInvoiceMasterReferback['id']), 'PaySupplierInvoiceMasterReferback with given id must be in DB');
        $this->assertModelData($paySupplierInvoiceMasterReferback, $createdPaySupplierInvoiceMasterReferback);
    }

    /**
     * @test read
     */
    public function testReadPaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->makePaySupplierInvoiceMasterReferback();
        $dbPaySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepo->find($paySupplierInvoiceMasterReferback->id);
        $dbPaySupplierInvoiceMasterReferback = $dbPaySupplierInvoiceMasterReferback->toArray();
        $this->assertModelData($paySupplierInvoiceMasterReferback->toArray(), $dbPaySupplierInvoiceMasterReferback);
    }

    /**
     * @test update
     */
    public function testUpdatePaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->makePaySupplierInvoiceMasterReferback();
        $fakePaySupplierInvoiceMasterReferback = $this->fakePaySupplierInvoiceMasterReferbackData();
        $updatedPaySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepo->update($fakePaySupplierInvoiceMasterReferback, $paySupplierInvoiceMasterReferback->id);
        $this->assertModelData($fakePaySupplierInvoiceMasterReferback, $updatedPaySupplierInvoiceMasterReferback->toArray());
        $dbPaySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepo->find($paySupplierInvoiceMasterReferback->id);
        $this->assertModelData($fakePaySupplierInvoiceMasterReferback, $dbPaySupplierInvoiceMasterReferback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->makePaySupplierInvoiceMasterReferback();
        $resp = $this->paySupplierInvoiceMasterReferbackRepo->delete($paySupplierInvoiceMasterReferback->id);
        $this->assertTrue($resp);
        $this->assertNull(PaySupplierInvoiceMasterReferback::find($paySupplierInvoiceMasterReferback->id), 'PaySupplierInvoiceMasterReferback should not exist in DB');
    }
}
