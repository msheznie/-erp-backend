<?php

use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceMasterRepositoryTest extends TestCase
{
    use MakePaySupplierInvoiceMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PaySupplierInvoiceMasterRepository
     */
    protected $paySupplierInvoiceMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->paySupplierInvoiceMasterRepo = App::make(PaySupplierInvoiceMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->fakePaySupplierInvoiceMasterData();
        $createdPaySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepo->create($paySupplierInvoiceMaster);
        $createdPaySupplierInvoiceMaster = $createdPaySupplierInvoiceMaster->toArray();
        $this->assertArrayHasKey('id', $createdPaySupplierInvoiceMaster);
        $this->assertNotNull($createdPaySupplierInvoiceMaster['id'], 'Created PaySupplierInvoiceMaster must have id specified');
        $this->assertNotNull(PaySupplierInvoiceMaster::find($createdPaySupplierInvoiceMaster['id']), 'PaySupplierInvoiceMaster with given id must be in DB');
        $this->assertModelData($paySupplierInvoiceMaster, $createdPaySupplierInvoiceMaster);
    }

    /**
     * @test read
     */
    public function testReadPaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->makePaySupplierInvoiceMaster();
        $dbPaySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepo->find($paySupplierInvoiceMaster->id);
        $dbPaySupplierInvoiceMaster = $dbPaySupplierInvoiceMaster->toArray();
        $this->assertModelData($paySupplierInvoiceMaster->toArray(), $dbPaySupplierInvoiceMaster);
    }

    /**
     * @test update
     */
    public function testUpdatePaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->makePaySupplierInvoiceMaster();
        $fakePaySupplierInvoiceMaster = $this->fakePaySupplierInvoiceMasterData();
        $updatedPaySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepo->update($fakePaySupplierInvoiceMaster, $paySupplierInvoiceMaster->id);
        $this->assertModelData($fakePaySupplierInvoiceMaster, $updatedPaySupplierInvoiceMaster->toArray());
        $dbPaySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepo->find($paySupplierInvoiceMaster->id);
        $this->assertModelData($fakePaySupplierInvoiceMaster, $dbPaySupplierInvoiceMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->makePaySupplierInvoiceMaster();
        $resp = $this->paySupplierInvoiceMasterRepo->delete($paySupplierInvoiceMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(PaySupplierInvoiceMaster::find($paySupplierInvoiceMaster->id), 'PaySupplierInvoiceMaster should not exist in DB');
    }
}
