<?php namespace Tests\Repositories;

use App\Models\POSSTAGInvoice;
use App\Repositories\POSSTAGInvoiceRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGInvoiceRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGInvoiceRepository
     */
    protected $pOSSTAGInvoiceRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGInvoiceRepo = \App::make(POSSTAGInvoiceRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->make()->toArray();

        $createdPOSSTAGInvoice = $this->pOSSTAGInvoiceRepo->create($pOSSTAGInvoice);

        $createdPOSSTAGInvoice = $createdPOSSTAGInvoice->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGInvoice);
        $this->assertNotNull($createdPOSSTAGInvoice['id'], 'Created POSSTAGInvoice must have id specified');
        $this->assertNotNull(POSSTAGInvoice::find($createdPOSSTAGInvoice['id']), 'POSSTAGInvoice with given id must be in DB');
        $this->assertModelData($pOSSTAGInvoice, $createdPOSSTAGInvoice);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->create();

        $dbPOSSTAGInvoice = $this->pOSSTAGInvoiceRepo->find($pOSSTAGInvoice->id);

        $dbPOSSTAGInvoice = $dbPOSSTAGInvoice->toArray();
        $this->assertModelData($pOSSTAGInvoice->toArray(), $dbPOSSTAGInvoice);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->create();
        $fakePOSSTAGInvoice = factory(POSSTAGInvoice::class)->make()->toArray();

        $updatedPOSSTAGInvoice = $this->pOSSTAGInvoiceRepo->update($fakePOSSTAGInvoice, $pOSSTAGInvoice->id);

        $this->assertModelData($fakePOSSTAGInvoice, $updatedPOSSTAGInvoice->toArray());
        $dbPOSSTAGInvoice = $this->pOSSTAGInvoiceRepo->find($pOSSTAGInvoice->id);
        $this->assertModelData($fakePOSSTAGInvoice, $dbPOSSTAGInvoice->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_invoice()
    {
        $pOSSTAGInvoice = factory(POSSTAGInvoice::class)->create();

        $resp = $this->pOSSTAGInvoiceRepo->delete($pOSSTAGInvoice->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGInvoice::find($pOSSTAGInvoice->id), 'POSSTAGInvoice should not exist in DB');
    }
}
