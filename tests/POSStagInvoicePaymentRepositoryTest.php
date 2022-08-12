<?php namespace Tests\Repositories;

use App\Models\POSStagInvoicePayment;
use App\Repositories\POSStagInvoicePaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSStagInvoicePaymentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSStagInvoicePaymentRepository
     */
    protected $pOSStagInvoicePaymentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSStagInvoicePaymentRepo = \App::make(POSStagInvoicePaymentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->make()->toArray();

        $createdPOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepo->create($pOSStagInvoicePayment);

        $createdPOSStagInvoicePayment = $createdPOSStagInvoicePayment->toArray();
        $this->assertArrayHasKey('id', $createdPOSStagInvoicePayment);
        $this->assertNotNull($createdPOSStagInvoicePayment['id'], 'Created POSStagInvoicePayment must have id specified');
        $this->assertNotNull(POSStagInvoicePayment::find($createdPOSStagInvoicePayment['id']), 'POSStagInvoicePayment with given id must be in DB');
        $this->assertModelData($pOSStagInvoicePayment, $createdPOSStagInvoicePayment);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->create();

        $dbPOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepo->find($pOSStagInvoicePayment->id);

        $dbPOSStagInvoicePayment = $dbPOSStagInvoicePayment->toArray();
        $this->assertModelData($pOSStagInvoicePayment->toArray(), $dbPOSStagInvoicePayment);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->create();
        $fakePOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->make()->toArray();

        $updatedPOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepo->update($fakePOSStagInvoicePayment, $pOSStagInvoicePayment->id);

        $this->assertModelData($fakePOSStagInvoicePayment, $updatedPOSStagInvoicePayment->toArray());
        $dbPOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepo->find($pOSStagInvoicePayment->id);
        $this->assertModelData($fakePOSStagInvoicePayment, $dbPOSStagInvoicePayment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_stag_invoice_payment()
    {
        $pOSStagInvoicePayment = factory(POSStagInvoicePayment::class)->create();

        $resp = $this->pOSStagInvoicePaymentRepo->delete($pOSStagInvoicePayment->id);

        $this->assertTrue($resp);
        $this->assertNull(POSStagInvoicePayment::find($pOSStagInvoicePayment->id), 'POSStagInvoicePayment should not exist in DB');
    }
}
