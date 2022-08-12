<?php namespace Tests\Repositories;

use App\Models\POSSOURCEPaymentGlConfigDetail;
use App\Repositories\POSSOURCEPaymentGlConfigDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSOURCEPaymentGlConfigDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSOURCEPaymentGlConfigDetailRepository
     */
    protected $pOSSOURCEPaymentGlConfigDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSOURCEPaymentGlConfigDetailRepo = \App::make(POSSOURCEPaymentGlConfigDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->make()->toArray();

        $createdPOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepo->create($pOSSOURCEPaymentGlConfigDetail);

        $createdPOSSOURCEPaymentGlConfigDetail = $createdPOSSOURCEPaymentGlConfigDetail->toArray();
        $this->assertArrayHasKey('id', $createdPOSSOURCEPaymentGlConfigDetail);
        $this->assertNotNull($createdPOSSOURCEPaymentGlConfigDetail['id'], 'Created POSSOURCEPaymentGlConfigDetail must have id specified');
        $this->assertNotNull(POSSOURCEPaymentGlConfigDetail::find($createdPOSSOURCEPaymentGlConfigDetail['id']), 'POSSOURCEPaymentGlConfigDetail with given id must be in DB');
        $this->assertModelData($pOSSOURCEPaymentGlConfigDetail, $createdPOSSOURCEPaymentGlConfigDetail);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->create();

        $dbPOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepo->find($pOSSOURCEPaymentGlConfigDetail->id);

        $dbPOSSOURCEPaymentGlConfigDetail = $dbPOSSOURCEPaymentGlConfigDetail->toArray();
        $this->assertModelData($pOSSOURCEPaymentGlConfigDetail->toArray(), $dbPOSSOURCEPaymentGlConfigDetail);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->create();
        $fakePOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->make()->toArray();

        $updatedPOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepo->update($fakePOSSOURCEPaymentGlConfigDetail, $pOSSOURCEPaymentGlConfigDetail->id);

        $this->assertModelData($fakePOSSOURCEPaymentGlConfigDetail, $updatedPOSSOURCEPaymentGlConfigDetail->toArray());
        $dbPOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepo->find($pOSSOURCEPaymentGlConfigDetail->id);
        $this->assertModelData($fakePOSSOURCEPaymentGlConfigDetail, $dbPOSSOURCEPaymentGlConfigDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->create();

        $resp = $this->pOSSOURCEPaymentGlConfigDetailRepo->delete($pOSSOURCEPaymentGlConfigDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSOURCEPaymentGlConfigDetail::find($pOSSOURCEPaymentGlConfigDetail->id), 'POSSOURCEPaymentGlConfigDetail should not exist in DB');
    }
}
