<?php namespace Tests\Repositories;

use App\Models\SRMTenderPaymentProof;
use App\Repositories\SRMTenderPaymentProofRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMTenderPaymentProofRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMTenderPaymentProofRepository
     */
    protected $sRMTenderPaymentProofRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMTenderPaymentProofRepo = \App::make(SRMTenderPaymentProofRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->make()->toArray();

        $createdSRMTenderPaymentProof = $this->sRMTenderPaymentProofRepo->create($sRMTenderPaymentProof);

        $createdSRMTenderPaymentProof = $createdSRMTenderPaymentProof->toArray();
        $this->assertArrayHasKey('id', $createdSRMTenderPaymentProof);
        $this->assertNotNull($createdSRMTenderPaymentProof['id'], 'Created SRMTenderPaymentProof must have id specified');
        $this->assertNotNull(SRMTenderPaymentProof::find($createdSRMTenderPaymentProof['id']), 'SRMTenderPaymentProof with given id must be in DB');
        $this->assertModelData($sRMTenderPaymentProof, $createdSRMTenderPaymentProof);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->create();

        $dbSRMTenderPaymentProof = $this->sRMTenderPaymentProofRepo->find($sRMTenderPaymentProof->id);

        $dbSRMTenderPaymentProof = $dbSRMTenderPaymentProof->toArray();
        $this->assertModelData($sRMTenderPaymentProof->toArray(), $dbSRMTenderPaymentProof);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->create();
        $fakeSRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->make()->toArray();

        $updatedSRMTenderPaymentProof = $this->sRMTenderPaymentProofRepo->update($fakeSRMTenderPaymentProof, $sRMTenderPaymentProof->id);

        $this->assertModelData($fakeSRMTenderPaymentProof, $updatedSRMTenderPaymentProof->toArray());
        $dbSRMTenderPaymentProof = $this->sRMTenderPaymentProofRepo->find($sRMTenderPaymentProof->id);
        $this->assertModelData($fakeSRMTenderPaymentProof, $dbSRMTenderPaymentProof->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->create();

        $resp = $this->sRMTenderPaymentProofRepo->delete($sRMTenderPaymentProof->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMTenderPaymentProof::find($sRMTenderPaymentProof->id), 'SRMTenderPaymentProof should not exist in DB');
    }
}
