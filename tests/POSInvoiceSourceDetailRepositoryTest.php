<?php namespace Tests\Repositories;

use App\Models\POSInvoiceSourceDetail;
use App\Repositories\POSInvoiceSourceDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSInvoiceSourceDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSInvoiceSourceDetailRepository
     */
    protected $pOSInvoiceSourceDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSInvoiceSourceDetailRepo = \App::make(POSInvoiceSourceDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->make()->toArray();

        $createdPOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepo->create($pOSInvoiceSourceDetail);

        $createdPOSInvoiceSourceDetail = $createdPOSInvoiceSourceDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSInvoiceSourceDetail);
        $this->assertNotNull($createdPOSInvoiceSourceDetail['id'], 'Created POSInvoiceSourceDetail must have id specified');
        $this->assertNotNull(POSInvoiceSourceDetail::find($createdPOSInvoiceSourceDetail['id']), 'POSInvoiceSourceDetail with given id must be in DB');
        $this->assertModelData($pOSInvoiceSourceDetail, $createdPOSInvoiceSourceDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->create();

        $dbPOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepo->find($pOSInvoiceSourceDetail->id);

        $dbPOSInvoiceSourceDetail = $dbPOSInvoiceSourceDetail->toArray();
        $this->assertModelData($pOSInvoiceSourceDetail->toArray(), $dbPOSInvoiceSourceDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->create();
        $fakePOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->make()->toArray();

        $updatedPOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepo->update($fakePOSInvoiceSourceDetail, $pOSInvoiceSourceDetail->id);

        $this->assertModelData($fakePOSInvoiceSourceDetail, $updatedPOSInvoiceSourceDetail->toArray());
        $dbPOSInvoiceSourceDetail = $this->pOSInvoiceSourceDetailRepo->find($pOSInvoiceSourceDetail->id);
        $this->assertModelData($fakePOSInvoiceSourceDetail, $dbPOSInvoiceSourceDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_invoice_source_detail()
    {
        $pOSInvoiceSourceDetail = factory(POSInvoiceSourceDetail::class)->create();

        $resp = $this->pOSInvoiceSourceDetailRepo->delete($pOSInvoiceSourceDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSInvoiceSourceDetail::find($pOSInvoiceSourceDetail->id), 'POSInvoiceSourceDetail should not exist in DB');
    }
}
