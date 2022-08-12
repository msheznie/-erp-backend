<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCEPaymentGlConfigDetail;

class POSSOURCEPaymentGlConfigDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_payment_gl_config_details', $pOSSOURCEPaymentGlConfigDetail
        );

        $this->assertApiResponse($pOSSOURCEPaymentGlConfigDetail);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_payment_gl_config_details/'.$pOSSOURCEPaymentGlConfigDetail->id
        );

        $this->assertApiResponse($pOSSOURCEPaymentGlConfigDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->create();
        $editedPOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_payment_gl_config_details/'.$pOSSOURCEPaymentGlConfigDetail->id,
            $editedPOSSOURCEPaymentGlConfigDetail
        );

        $this->assertApiResponse($editedPOSSOURCEPaymentGlConfigDetail);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_payment_gl_config_detail()
    {
        $pOSSOURCEPaymentGlConfigDetail = factory(POSSOURCEPaymentGlConfigDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_payment_gl_config_details/'.$pOSSOURCEPaymentGlConfigDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_payment_gl_config_details/'.$pOSSOURCEPaymentGlConfigDetail->id
        );

        $this->response->assertStatus(404);
    }
}
