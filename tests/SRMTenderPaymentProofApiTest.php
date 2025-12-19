<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SRMTenderPaymentProof;

class SRMTenderPaymentProofApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_r_m_tender_payment_proofs', $sRMTenderPaymentProof
        );

        $this->assertApiResponse($sRMTenderPaymentProof);
    }

    /**
     * @test
     */
    public function test_read_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_r_m_tender_payment_proofs/'.$sRMTenderPaymentProof->id
        );

        $this->assertApiResponse($sRMTenderPaymentProof->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->create();
        $editedSRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_r_m_tender_payment_proofs/'.$sRMTenderPaymentProof->id,
            $editedSRMTenderPaymentProof
        );

        $this->assertApiResponse($editedSRMTenderPaymentProof);
    }

    /**
     * @test
     */
    public function test_delete_s_r_m_tender_payment_proof()
    {
        $sRMTenderPaymentProof = factory(SRMTenderPaymentProof::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_r_m_tender_payment_proofs/'.$sRMTenderPaymentProof->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_r_m_tender_payment_proofs/'.$sRMTenderPaymentProof->id
        );

        $this->response->assertStatus(404);
    }
}
