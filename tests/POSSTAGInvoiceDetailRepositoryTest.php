<?php namespace Tests\Repositories;

use App\Models\POSSTAGInvoiceDetail;
use App\Repositories\POSSTAGInvoiceDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGInvoiceDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGInvoiceDetailRepository
     */
    protected $pOSSTAGInvoiceDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGInvoiceDetailRepo = \App::make(POSSTAGInvoiceDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->make()->toArray();

        $createdPOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepo->create($pOSSTAGInvoiceDetail);

        $createdPOSSTAGInvoiceDetail = $createdPOSSTAGInvoiceDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGInvoiceDetail);
        $this->assertNotNull($createdPOSSTAGInvoiceDetail['id'], 'Created POSSTAGInvoiceDetail must have id specified');
        $this->assertNotNull(POSSTAGInvoiceDetail::find($createdPOSSTAGInvoiceDetail['id']), 'POSSTAGInvoiceDetail with given id must be in DB');
        $this->assertModelData($pOSSTAGInvoiceDetail, $createdPOSSTAGInvoiceDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->create();

        $dbPOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepo->find($pOSSTAGInvoiceDetail->id);

        $dbPOSSTAGInvoiceDetail = $dbPOSSTAGInvoiceDetail->toArray();
        $this->assertModelData($pOSSTAGInvoiceDetail->toArray(), $dbPOSSTAGInvoiceDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->create();
        $fakePOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->make()->toArray();

        $updatedPOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepo->update($fakePOSSTAGInvoiceDetail, $pOSSTAGInvoiceDetail->id);

        $this->assertModelData($fakePOSSTAGInvoiceDetail, $updatedPOSSTAGInvoiceDetail->toArray());
        $dbPOSSTAGInvoiceDetail = $this->pOSSTAGInvoiceDetailRepo->find($pOSSTAGInvoiceDetail->id);
        $this->assertModelData($fakePOSSTAGInvoiceDetail, $dbPOSSTAGInvoiceDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_invoice_detail()
    {
        $pOSSTAGInvoiceDetail = factory(POSSTAGInvoiceDetail::class)->create();

        $resp = $this->pOSSTAGInvoiceDetailRepo->delete($pOSSTAGInvoiceDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGInvoiceDetail::find($pOSSTAGInvoiceDetail->id), 'POSSTAGInvoiceDetail should not exist in DB');
    }
}
