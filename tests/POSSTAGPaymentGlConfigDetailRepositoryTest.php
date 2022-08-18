<?php namespace Tests\Repositories;

use App\Models\POSSTAGPaymentGlConfigDetail;
use App\Repositories\POSSTAGPaymentGlConfigDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSTAGPaymentGlConfigDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSTAGPaymentGlConfigDetailRepository
     */
    protected $pOSSTAGPaymentGlConfigDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSTAGPaymentGlConfigDetailRepo = \App::make(POSSTAGPaymentGlConfigDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->make()->toArray();

        $createdPOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepo->create($pOSSTAGPaymentGlConfigDetail);

        $createdPOSSTAGPaymentGlConfigDetail = $createdPOSSTAGPaymentGlConfigDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSSTAGPaymentGlConfigDetail);
        $this->assertNotNull($createdPOSSTAGPaymentGlConfigDetail['id'], 'Created POSSTAGPaymentGlConfigDetail must have id specified');
        $this->assertNotNull(POSSTAGPaymentGlConfigDetail::find($createdPOSSTAGPaymentGlConfigDetail['id']), 'POSSTAGPaymentGlConfigDetail with given id must be in DB');
        $this->assertModelData($pOSSTAGPaymentGlConfigDetail, $createdPOSSTAGPaymentGlConfigDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->create();

        $dbPOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepo->find($pOSSTAGPaymentGlConfigDetail->id);

        $dbPOSSTAGPaymentGlConfigDetail = $dbPOSSTAGPaymentGlConfigDetail->toArray();
        $this->assertModelData($pOSSTAGPaymentGlConfigDetail->toArray(), $dbPOSSTAGPaymentGlConfigDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->create();
        $fakePOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->make()->toArray();

        $updatedPOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepo->update($fakePOSSTAGPaymentGlConfigDetail, $pOSSTAGPaymentGlConfigDetail->id);

        $this->assertModelData($fakePOSSTAGPaymentGlConfigDetail, $updatedPOSSTAGPaymentGlConfigDetail->toArray());
        $dbPOSSTAGPaymentGlConfigDetail = $this->pOSSTAGPaymentGlConfigDetailRepo->find($pOSSTAGPaymentGlConfigDetail->id);
        $this->assertModelData($fakePOSSTAGPaymentGlConfigDetail, $dbPOSSTAGPaymentGlConfigDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_t_a_g_payment_gl_config_detail()
    {
        $pOSSTAGPaymentGlConfigDetail = factory(POSSTAGPaymentGlConfigDetail::class)->create();

        $resp = $this->pOSSTAGPaymentGlConfigDetailRepo->delete($pOSSTAGPaymentGlConfigDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSTAGPaymentGlConfigDetail::find($pOSSTAGPaymentGlConfigDetail->id), 'POSSTAGPaymentGlConfigDetail should not exist in DB');
    }
}
