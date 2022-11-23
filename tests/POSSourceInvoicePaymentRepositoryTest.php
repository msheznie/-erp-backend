<?php namespace Tests\Repositories;

use App\Models\POSSourceInvoicePayment;
use App\Repositories\POSSourceInvoicePaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSourceInvoicePaymentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSourceInvoicePaymentRepository
     */
    protected $pOSSourceInvoicePaymentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSourceInvoicePaymentRepo = \App::make(POSSourceInvoicePaymentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->make()->toArray();

        $createdPOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepo->create($pOSSourceInvoicePayment);

        $createdPOSSourceInvoicePayment = $createdPOSSourceInvoicePayment->toArray();
        $this->assertArrayHasKey('id', $createdPOSSourceInvoicePayment);
        $this->assertNotNull($createdPOSSourceInvoicePayment['id'], 'Created POSSourceInvoicePayment must have id specified');
        $this->assertNotNull(POSSourceInvoicePayment::find($createdPOSSourceInvoicePayment['id']), 'POSSourceInvoicePayment with given id must be in DB');
        $this->assertModelData($pOSSourceInvoicePayment, $createdPOSSourceInvoicePayment);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->create();

        $dbPOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepo->find($pOSSourceInvoicePayment->id);

        $dbPOSSourceInvoicePayment = $dbPOSSourceInvoicePayment->toArray();
        $this->assertModelData($pOSSourceInvoicePayment->toArray(), $dbPOSSourceInvoicePayment);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->create();
        $fakePOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->make()->toArray();

        $updatedPOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepo->update($fakePOSSourceInvoicePayment, $pOSSourceInvoicePayment->id);

        $this->assertModelData($fakePOSSourceInvoicePayment, $updatedPOSSourceInvoicePayment->toArray());
        $dbPOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepo->find($pOSSourceInvoicePayment->id);
        $this->assertModelData($fakePOSSourceInvoicePayment, $dbPOSSourceInvoicePayment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_source_invoice_payment()
    {
        $pOSSourceInvoicePayment = factory(POSSourceInvoicePayment::class)->create();

        $resp = $this->pOSSourceInvoicePaymentRepo->delete($pOSSourceInvoicePayment->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSourceInvoicePayment::find($pOSSourceInvoicePayment->id), 'POSSourceInvoicePayment should not exist in DB');
    }
}
